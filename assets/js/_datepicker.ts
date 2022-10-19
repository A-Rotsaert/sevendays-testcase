import Datepicker from "../../node_modules/vanillajs-datepicker/js/Datepicker.js";

const datepickers = document.querySelectorAll('.datepicker');
for (let i = 0; i < datepickers.length; i++) {
    new Datepicker(datepickers[i], {
        'format': 'dd/mm/yyyy'
    });
}

