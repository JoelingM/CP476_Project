const ACCEPTED_TYPES = ["jpg", "png", "jpeg", "bmp"];

$(document).ready(() => {

    let postLimit = 5; // How many posts to load.
    let start = 0; // Start offset.
    let isLoading = false; // True if loading.
    let targetId = undefined;

    function loadAllPosts(limit, start, uid) {
        let data = {
            limit: limit,
            start: start
        };
        if (uid !== undefined) {
            data["uid"] = uid;
        }
        $.ajax({
            url: "../requests/post-load.php",
            method: "POST",
            data: data,
            cache: false,
            success: function (data) {
                $('#post-area').append(data);
                if (data.trim() == '') {
                    $('#load-message').html("<span class='loading'>Uh-oh, looks like there's no more posts!</span>");
                    isLoading = true;
                } else {
                    $('#load-message').html("<span class='loading'>Loading More Posts...</span>");
                    isLoading = false;
                }
            }
        });
    }

    if (!isLoading) {
        isLoading = true;
        if ($('#post-area').hasClass('only-me')) {

            // Get the uid of the person whose page we're on.
            let params = window.location.search.substr(1).split("?");
            for (let param of params) {
                if (param.split("=")[0] === "uid") {
                    targetId = param.split("=")[1];
                }
            }
        }
        loadAllPosts(postLimit, start, targetId);
    }

    //Load more posts when scrolling to bottom of page.
    $(window).scroll(() => {
        if ($(window).scrollTop() + $(window).height() > $("#post-area").height() && !isLoading) {
            isLoading = true;
            start += postLimit;
            setTimeout(() => {
                loadAllPosts(postLimit, start, targetId);
            }, 750);
        }
    });

    /*
    Submits a new reply to the database before regrabbing the posts replies
    these replies are echo'd to html then sent back as data
    the post is cleared of all replies and the new replies are prepended before the reply-form
    */
    $("#post-area").on('submit', '.reply-form', function (event) {
        event.preventDefault(); //prevent default isLoading 
        let $reply = $(this);

        $.ajax({
            url: "../requests/post-reply.php",
            type: "POST",
            cache: false,
            data: $reply.serialize(),
            success: function (data) {
                if (data.trim() != '') {
                    let $post = $reply.parent();
                    $post.find('.reply').remove();
                    $post.prepend(data);
                    $reply.find(".comment-box").val('');
                }
            }
        })
    });

    $("#post-area").on('submit', '.like-post-form', function (event) {
        event.preventDefault(); //prevent default isLoading 
        let $postLike = $(this);

        $.ajax({
            url: "../requests/post-like.php",
            type: "POST",
            cache: false,
            data: $postLike.serialize(),
            success: function (data) {
                if (data.trim() != '') {
                    let $post = $postLike.parent();
                    $post.find('.like-post-form').remove();
                    $post.prepend(data);
                }
            }
        })
    });

    /*
    Delete a post, runs a simple database query
    then visually removes the closest html of type post
    which is the current post as it's parent is closest.
    */
    $("#post-area").on('submit', '.delete-post-form', function (event) {

        event.preventDefault();
        let $deleteForm = $(this);

        $.ajax({
            url: "../requests/post-delete.php",
            type: "POST",
            cache: false,
            data: $deleteForm.serialize(),
            success: function (data) {
                $deleteForm.closest(".post").remove();
            }
        })
    });

    /*
    Delete a specific reply. Runs a database delete query
    then visually removes the closest .reply class, which is
    it's parent.
    */
    $("#post-area").on('submit', '.delete-reply-form', function (event) {

        event.preventDefault();
        let $deleteForm = $(this);

        $.ajax({
            url: "../requests/post-reply-delete.php",
            type: "POST",
            cache: false,
            data: $deleteForm.serialize(),
            success: function (data) {
                $deleteForm.closest(".reply").remove();
            }
        })
    });

    // Register an event listener for a changed picture.
    $("#pic-upload").on("change", () => {
        let fileName = $("#pic-upload")[0].files[0].name;
        if (fileName.lastIndexOf(".") !== -1) {
            let extension = fileName.substring(fileName.lastIndexOf(".") + 1).toLowerCase();
            if (ACCEPTED_TYPES.includes(extension)) {
                let reader = new FileReader();
                reader.onload = (event) => {
                    $("#preview-img").attr("src", event.target.result);
                    $("#preview-img").css("display", "block");
                    $("#pic-upload-label").css("display", "none");
                    $("#pic-upload").css("display", "none");
                }
                reader.readAsDataURL($("#pic-upload")[0].files[0]);
            } else {
                alert(`Please select a file of one of the following types: ${ACCEPTED_TYPES.join(", ")}.`);
            }
        } else {
            alert(`Please select a file of one of the following types: ${ACCEPTED_TYPES.join(", ")}.`);
        }
    });

    $("#joke-button").on("click", () => {
        $.ajax({
            url: "https://icanhazdadjoke.com/",
            method: "GET",
            headers: {
                Accept: "text/plain"
            },
            success: function (joke) {
                $("#post-box").html(`${$("#post-box").innerHTML ? $("#post-box").innerHTML + " " : ""}${joke}`);
            }
        });
    });
});