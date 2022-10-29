import IMask from "imask";
var telephone = document.getElementById("telephone");
var fax = document.getElementById("fax");
var maskOptions = {
    mask: [
        {
            mask: "(\\000) 000-0000",
            // lazy: false
        },
        {
            mask: "(\\0000) 000-0000",
            // lazy: false
        },
    ],
};
var mask = IMask(telephone, maskOptions);
var mask = IMask(fax, maskOptions);
