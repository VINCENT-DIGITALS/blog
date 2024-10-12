
$(document).ready(function() {
    // Load blogs initially (without filter)
    loadBlogs();

    // Add event listener to the search input
    $('#searchInput').on('keyup', function() {
        var searchTerm = $(this).val().trim();
        loadBlogs(searchTerm); // Call loadBlogs with the search term
    });

    // AJAX to load blogs (with optional searchTerm)
    function loadBlogs(searchTerm = '') {
        $.ajax({
            url: "../../db/request.php",
            method: "POST",
            data: {
                "get_post": true,
                "search": searchTerm // Send the search term to the server
            },
            success: function(result) {
                try {
                    var datas = JSON.parse(result);

                    var grid = ``;
                    if (datas.length === 0) {
                        grid = '<p>No blogs found.</p>';
                    } else {
                        datas.forEach(function(data) {
                            // Define truncated and full content
                            var truncatedContent = data['content'].substring(0, 100);
                            var fullContent = data['content'];
                            var contentToShow = fullContent.length > 100 ? `${truncatedContent}... <a href="#" class="toggleContent" data-full="${fullContent}" data-truncated="${truncatedContent}">Show More</a>` : fullContent;

                            grid += `
                            <div class="blog-card" data-post-id="${data['id']}">
                                <h3>${data['title']}</h3>
                                <p><strong>Author:</strong> ADMIN</p>
                                <p>${contentToShow}</p>
                                <p><strong>Date:</strong> ${data['created_at']}</p>
                                <div class="like-share">
                                    <button class="like-button">Like</button>
                                    <button class="share-button">Share</button>
                                </div>
                                <div class="comments-section">
                                    <h4>Comments:</h4>
                                    ${isLoggedIn && !isAdmin ? `
                                    <textarea placeholder="Add a comment..."></textarea>
                                    <button class="submit-comment">Submit</button>` : ''}
                                    <div class="comments-list" id="comments-${data['id']}">
                                        <!-- Submitted comments will be appended here -->
                                    </div>
                                </div>

                            </div>`;

                        });
                    }
                    $('#gridBlogs').html(grid);


                    // Load comments for each blog
                    datas.forEach((blog) => {
                        console.log(`Loading comments for blog ID: ${blog.id}`); // Log the blog ID
                        loadComments(blog.id);
                    });


                    // Handle Show More/Show Less for truncated content
                    $('#gridBlogs').on('click', '.toggleContent', function(event) {
                        event.preventDefault();
                        var isTruncated = $(this).text() === "Show More";
                        var fullContent = $(this).data('full');
                        var truncatedContent = $(this).data('truncated');
                        if (isTruncated) {
                            $(this).parent().html(`${fullContent} <a href="#" class="toggleContent" data-full="${fullContent}" data-truncated="${truncatedContent}">Show Less</a>`);
                        } else {
                            $(this).parent().html(`${truncatedContent}... <a href="#" class="toggleContent" data-full="${fullContent}" data-truncated="${truncatedContent}">Show More</a>`);
                        }
                    });

                    // Handle comment submission
                    $('#gridBlogs').on('click', '.submit-comment', function(e) {
                        e.preventDefault(); // Prevent default form submission

                        var commentTextarea = $(this).siblings('textarea');
                        var comment = commentTextarea.val();
                        var postId = $(this).closest('.blog-card').data('post-id'); // Get the post ID
                        var commentSection = $(this).closest('.comments-section').find('.comments-list'); // Get the specific comment section

                        if (comment) {
                            // AJAX request to submit the comment
                            $.ajax({
                                url: "../../db/request.php",
                                method: "POST",
                                data: {
                                    "submit_comment": true,
                                    "comment": comment,
                                    "post_id": postId,
                                    "user_id": userId // userId is passed from PHP
                                },
                                success: function(result) {
                                    try {
                                        var response = JSON.parse(result);
                                        if (response.success) {
                                            // Prepend the new comment to the comments list
                                            var newCommentHtml = `<p><strong>${response.username}:</strong> ${comment}</p>`;
                                            commentSection.prepend(newCommentHtml); // Use prepend instead of append

                                            commentTextarea.val(''); // Clear the textarea after submission
                                        } else {
                                            alert("Failed to submit comment: " + response.error);
                                        }
                                    } catch (error) {
                                        console.error("Error parsing response. Raw response was:", result); // Log raw response
                                        alert("There was an error. Please try again later.");
                                    }
                                },
                                error: function() {
                                    alert("Error submitting comment.");
                                }
                            });
                        } else {
                            alert("Please enter a comment.");
                        }
                    });




                    // Handle like button click
                    $('#gridBlogs').on('click', '.like-button', function() {
                        alert("You liked this post!");
                        // Implement like functionality (e.g., update database) here
                    });

                    // Handle share button click
                    $('#gridBlogs').on('click', '.share-button', function() {
                        alert("Post shared!");
                        // Implement share functionality (e.g., share on social media) here
                    });
                } catch (error) {
                    console.error("Parsing error:", error);
                    console.error("Server response:", result);
                    alert("Failed to load data!");
                }
            },
            error: function() {
                alert("Something went wrong!");
            }
        });
    }

    // Function to load comments for a specific post
    function loadComments(postId) {
        console.log(`Attempting to load comments for post ID: ${postId}`);

        $.ajax({
            url: "../../db/request.php",
            method: "POST",
            data: {
                load_comments: true,
                post_id: postId
            },
            success: function(result) {
                console.log(`Raw response for post ID ${postId}:`, result);

                if (result === null) {
                    console.error(`No response received for post ID ${postId}`);
                    alert('No comments found.');
                    return;
                }

                try {
                    const comments = JSON.parse(result);

                    if (comments.error) {
                        console.error(`Error from server: ${comments.error}`);
                        alert(`Failed to load comments: ${comments.error}`);
                        return;
                    }

                    console.log(`Parsed comments for post ID ${postId}:`, comments);

                    let commentHtml = '';

                    if (comments.length === 0) {
                        console.log(`No comments found for post ID ${postId}`);
                        commentHtml = '<p>No comments yet.</p>';
                    } else {
                        // Only show the first comment initially
                        commentHtml += `<p><strong>${comments[0].username}:</strong> ${comments[0].comment}</p>`;

                        // If there are more comments, add a "Show More" link
                        if (comments.length > 1) {
                            commentHtml += `<a href="#" class="show-more-comments" data-post-id="${postId}">Show More</a>`;
                            commentHtml += `<div class="more-comments" style="display: none;">`;

                            // Loop through remaining comments and hide them initially
                            for (let i = 1; i < comments.length; i++) {
                                commentHtml += `<p><strong>${comments[i].username}:</strong> ${comments[i].comment}</p>`;
                            }

                            commentHtml += `</div>`;
                            commentHtml += `<a href="#" class="show-less-comments" data-post-id="${postId}" style="display: none;">Show Less</a>`;
                        }
                    }

                    // Use the correct ID to append comments
                    $(`#comments-${postId}`).html(commentHtml);
                } catch (error) {
                    console.error(`Error parsing JSON for post ID ${postId}:`, error);
                    console.error('Raw response:', result);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error(`AJAX request failed: ${textStatus}`, errorThrown);
                alert('Failed to load comments.');
            }
        });
    }

    // Event listener for Show More functionality for comments
    $('#gridBlogs').on('click', '.show-more-comments', function(event) {
        event.preventDefault();
        var postId = $(this).data('post-id');

        // Show all comments and switch to "Show Less"
        $(this).siblings('.more-comments').show();
        $(this).siblings('.show-less-comments').show();
        $(this).hide();
    });

    // Event listener for Show Less functionality for comments
    $('#gridBlogs').on('click', '.show-less-comments', function(event) {
        event.preventDefault();
        var postId = $(this).data('post-id');

        // Hide extra comments and switch back to "Show More"
        $(this).siblings('.more-comments').hide();
        $(this).siblings('.show-more-comments').show();
        $(this).hide();
    });



    // Handle like button click
    $('#gridBlogs').on('click', '.like-button', function() {
        alert('You liked this post!');
        // Implement like functionality
    });

    // Handle share button click
    $('#gridBlogs').on('click', '.share-button', function() {
        alert('Post shared!');
        // Implement share functionality
    });
});
