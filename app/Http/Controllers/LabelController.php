<?php

namespace App\Http\Controllers;

use App\event_subject;
use Illuminate\Http\Request;
use App\Library\PDF_Label;

class LabelController extends PDF_Label
{
    private $fpdf;

    public function __construct()
    {
        define('FPDF_FONTPATH', public_path() . '/font');
    }

    public function createPDF()
    {
        $events = event_subject::where('labelStatus', '1')
            ->join('subjects', 'subject_id', 'subjects.id')
            ->join('events', 'event_id', 'events.id')
            ->join('arms', 'events.arm_id', 'arms.id')
            ->select([
                'subjects.project_id',
                'arms.name AS armname',
                'events.name AS eventname',
                'events.id AS event_id',
                'subjectID',
                'firstname',
                'surname',
                'subject_event_labels',
                'name_labels',
                'study_id_labels'
            ])
            ->where('subjects.project_id', session('currentProject'))
            ->where('user_id', auth()->user()->id)
            ->where('active', true)
            ->get();

        /*------------------------------------------------
            To create the object, 2 possibilities:
            either pass a custom format via an array
            or use a built-in AVERY name
            ------------------------------------------------*/

        // Example of custom format
        // $pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>2, 'NY'=>7, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>99, 'height'=>38, 'font-size'=>14));

        // Standard format
        $this->fpdf = new PDF_Label('L7651_mod');

        $this->fpdf->AddPage();
        $this->fpdf->AddFont('Calibri', '', 'calibri.php');
        $this->fpdf->SetFont('Calibri', '', 8);
        // $this->fpdf->AddFont('EBGaramond', '', 'EBGaramond-VariableFont_wght.php');
        // $this->fpdf->SetFont('EBGaramond', '', 8);

        foreach ($events as $event) {
            // Generate Name labels
            $PSE = $event->project_id . '_' . $event->subjectID . '_' . $event->event_id;
            for ($i = 0; $i < $event->name_labels; $i++) {
                $text = sprintf("%s %s\n%s\nEvent: %s\nArm: %s", $event->firstname, $event->surname, $PSE, $event->eventname, $event->armname);
                $this->fpdf->Add_BarLabel($text,$PSE);
            }
            // Generate Study ID labels
            for ($i = 0; $i < $event->study_id_labels; $i++) {
                $text = sprintf("%s", $event->subjectID);
                $this->fpdf->Add_BarLabel($text,$event->subjectID);
            }
            // Generate PSE labels
            for ($i = 0; $i < $event->subject_event_labels; $i++) {
                $text = sprintf("%s\nEvent: %s\nArm: %s", $PSE, $event->eventname, $event->armname);
                $this->fpdf->Add_BarLabel($text,$PSE);
            }
        }

        $this->fpdf->Output("labels.pdf", "D");
    }
}
