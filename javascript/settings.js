const ACCEPTED_TYPES = ["jpg", "png", "jpeg", "bmp"];

$(($) => {
    // Change what input fields are required based on what input fields are filled.
    function updateRequirements() {
        $("#current-password").attr("required", 
            !!$("#change-email-1").val() || 
            !!$("#change-email-2").val() || 
            !!$("#change-password-1").val() ||
            !!$("#change-password-2").val()
            );

        $("#change-email-1").attr("required", !!$("#change-email-2").val());
        $("#change-email-2").attr("required", !!$("#change-email-1").val());

        $("#change-password-1").attr("required", !!$("#change-password-2").val());
        $("#change-password-2").attr("required", !!$("#change-password-1").val());
    }

    // Register an event listener for a changed profile picture.
    $("#profile-pic-upload").on("change", () => {
        let fileName = $("#profile-pic-upload")[0].files[0].name;
        if (fileName.lastIndexOf(".") !== -1) {
            let extension = fileName.substring(fileName.lastIndexOf(".") + 1).toLowerCase();
            if (ACCEPTED_TYPES.includes(extension)) {
                let reader = new FileReader();
                reader.onload = (event) => {
                    $("#profile-pic").attr("src", event.target.result);
                }
                reader.readAsDataURL($("#profile-pic-upload")[0].files[0]);
            } else {
                alert(`Please select a file of one of the following types: ${ACCEPTED_TYPES.join(", ")}.`);
            }
        } else {
            alert(`Please select a file of one of the following types: ${ACCEPTED_TYPES.join(", ")}.`);
        }
    });

    // Register an event listener to make sure the emails are the same.
    $("#change-email-1, #change-email-2").on("keyup", () => {
        if ($("#change-email-1").val() !== $("#change-email-2").val()) {
            $("#change-email-2").addClass("bad-input");
        } else {
            $("#change-email-2").removeClass("bad-input");
        }
        updateRequirements();
    });

    // Register an event listener to make sure the passwords are the same.
    $("#change-password-1, #change-password-2").on("keyup", () => {
        if ($("#change-password-1").val() !== $("#change-password-2").val()) {
            $("#change-password-2").addClass("bad-input");
        } else {
            $("#change-password-2").removeClass("bad-input");
        }
        updateRequirements();
    });

    // Register an event listener to make sure the emails and passwords equal each other when the submit button is pressed.
    $("#submit").on("click", (event) => {
        if ($("#change-email-1").val() !== $("#change-email-2").val()) {
            alert("The emails are not equal.");
            event.preventDefault();
        } else if ($("#change-password-1").val() !== $("#change-password-2").val()) {
            alert("The passwords are not equal.");
            event.preventDefault();
        }
    });

});