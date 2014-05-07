function cepValue(codLogradouro) {
    switch(codLogradouro) {
        case "7774136":
            return "90810240";
        case "7773054":
            return "90810220";
        case "7774094":
            return "90810230";
        case "7774110":
            return "90810190";
        case "7774102":
            return "98810210";
        case "7774128":
            return "90850050";
        case "7774086":
            return "90850110";
        default:
            return "";
    }
}

function loadCep() {
    window.document.getElementById("logradouroCep").value = cepValue(window.document.getElementById("logradouroCod").value);
}


