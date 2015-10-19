jQuery.validator.addMethod("alphanumeric", function (value, element) {
    var stringa = new String(value);
    stringa = stringa.replace(" ", "");
    stringa = stringa.replace("-", "");
    stringa = stringa.replace("_", "");
    return this.optional(element) || /^[a-zA-Z0-9]+$/.test(stringa.valueOf());
});


$(document).ready(function () {
    $('#form-registrazione').validate({
        errorElement: "span",
        rules: {
            username: {
                minlength: 6,
                maxlength: 15,
                alphanumeric: true,
                required: true
            },
            email: {
                required: true,
                email: true,
                
            },
            password: {
                minlength: 8,
                required: true
            },
        },
        messages: {
            username: {
                required: "Scegli il tuo nome utente.",
                minlength: "Inserisci almeno almeno 6 caratteri.",
                maxlength: "Inserisci meno di 15 caratteri.",
                alphanumeric: "Si accettano soltanto caratteri alfanumerici, spazi, trattini e underscore.",
                remote: "L'username &egrave; gi&agrave; utilizzato da un altro giocatore, per favore scegline un altro."
            },
            password: {
                required: "Imposta una password.",
                minlength: "La password deve essere lunga almeno 8 caratteri.",
            },
            email: {
                required: "Inserisci un indirizzo email.",
                email: "L'indirizzo email inserito non &egrave; corretto.",
                remote: "L'email &egrave; gi&agrave; utilizzata da un altro giocatore, puoi utilizzare un'altra email oppure <a href='recover.php'>recuperare i dati del tuo account</a>."
            }
        },
        highlight: function (label) {
            $(label).closest('.input-text').removeClass("success").addClass('error');
        },
        success: function (label) {
            label.addClass('valid')
                .closest('.input-text').addClass('success');
        }
    });
});