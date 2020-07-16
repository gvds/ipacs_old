@extends('layouts.app')

@section('content')
  <html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Edit Role Permissions</title>
  </head>
  <body>
    <div class='row'>
      <div class='col-md col-6'>
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <div class='col-md-3'>
          <div class='h1'>
            {{$role->name}}
          </div>
        </div>
        <div class='row'>
          <div class='col-md-2'>
            {!! Form::open(['url' => ["roles/$role->id/permissions"], 'method' => 'post']) !!}
            <table class="table">
              @foreach ($permissions as $permission_id => $permission)
                <tr>
                  <th>{{$permission}}</th><td>{!! Form::checkbox($permission, 1, array_key_exists($permission_id,$rolepermissions)) !!}</td>
                </tr>
              @endforeach
            </table>
            {!! Form::submit('Save Record', ['class' => "btn btn-primary"]) !!}
            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>

  </body>
  </html>
@endsection
