import {Toast} from "bootstrap";

let toastElList = [].slice.call(document.querySelectorAll('.toast'));
toastElList.map(function (toastTriggerEl) {
    new Toast(toastTriggerEl).show();
});