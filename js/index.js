$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const urlpostId = urlParams.get('id');
    // Log the retrieved id to the console

    console.log("Retrieved post type ID of:", urlpostId);
    // Load a specific post if postId is present in the URL
    if (urlpostId) {
        const postIdInt = parseInt(urlpostId, 10);  // Convert to integer
        loadBlogs('', '', postIdInt);  // Pass postId as an integer
        // console.log("Converted post ID to integer:",typeof postIdInt );
    } else {
        loadBlogs();  // Otherwise, load all blogs
    }

    // Add event listener to the search input
    $('#searchInput').on('keyup', function () {
        var searchTerm = $(this).val().trim();
        loadBlogs(searchTerm); // Call loadBlogs with the search term
    });

    $('#applyFilter').on('click', function () {
        var searchTerm = $('#searchInput').val().trim();
        var categoryFilter = $('#categoryFilter').val();

        console.log("Retrieved post ID from URL:", categoryFilter);

        loadBlogs(searchTerm, categoryFilter);
    });

    // AJAX to load blogs (with optional searchTerm)
    function loadBlogs(searchTerm = '', category = '', urlpostId = null) {
        $.ajax({
            url: "/Blog/db/request.php",
            method: "POST",
            data: {
                "get_post": true,
                "search": searchTerm, // Send the search term to the server
                "category": category,
                "url_post_id": urlpostId

            },
            success: function (result) {
                try {
                    var datas = JSON.parse(result);
                    // Sort the data by 'created_at' timestamp (newest first)
                    datas.sort((a, b) => {
                        const dateA = new Date(a.created_at);
                        const dateB = new Date(b.created_at);
                        return dateB - dateA; // Descending order
                    });
                    var grid = ``;
                    if (datas.length === 0) {
                        grid = '<p>No blogs found.</p>';
                    } else {
                        datas.forEach(function (data) {
                            var truncatedContent = data['content'].substring(0, 100);
                            var fullContent = data['content'];
                            var contentToShow = fullContent.length > 100 ? `${truncatedContent}... <a href="#" class="toggleContent" data-full="${fullContent}" data-truncated="${truncatedContent}">Show More</a>` : fullContent;


                            grid += `
                            <div class="blog-card" data-post-id="${data['id']}">
                                <h3>${data['title']}</h3>
                                <p><strong>Author:</strong> ADMIN</p>
                                <p><strong>Category:</strong> ${data['category']}</p>
                                <p>${contentToShow}</p>
                                
                                <p><strong>Date:</strong> ${data['created_at']}</p>
                               
                                <div class="like-share">
                                    ${!isAdmin ? ` <button class="like-button">Like (${data['likes_count']})</button> <!-- Display the number of likes -->  ` : ''}
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
                        loadComments(blog.id);
                    });

                    // Handle Show More/Show Less for truncated content
                    $('#gridBlogs').on('click', '.toggleContent', function (event) {
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
                    $('#gridBlogs').on('click', '.submit-comment', function (e) {
                        e.preventDefault();
                        var commentTextarea = $(this).siblings('textarea');
                        var comment = commentTextarea.val();
                        var postId = $(this).closest('.blog-card').data('post-id');
                        var commentSection = $(this).closest('.comments-section').find('.comments-list');
                        var moreCommentsDiv = commentSection.find('.more-comments');

                        if (comment) {
                            $.ajax({
                                url: "/Blog/db/request.php",
                                method: "POST",
                                data: {
                                    "submit_comment": true,
                                    "comment": comment,
                                    "post_id": postId,
                                    "user_id": userId // userId is passed from PHP
                                },
                                success: function (result) {
                                    try {
                                        var response = JSON.parse(result);
                                        if (response.success) {
                                            // Remove "No comments yet" if it exists
                                            commentSection.find('p:contains("No comments yet")').remove();

                                            // Create a new comment HTML
                                            var newCommentHtml = `<p><strong>${response.username}:</strong> ${comment}</p>`;

                                            // Check if the .more-comments div exists
                                            if (moreCommentsDiv.length === 0) {
                                                // Create the scrollable div if it doesn't exist
                                                commentSection.prepend('<div class="more-comments" style="max-height: 150px; overflow-y: auto;"></div>');
                                                moreCommentsDiv = commentSection.find('.more-comments');
                                            }

                                            // Add the new comment to the scrollable area
                                            moreCommentsDiv.prepend(newCommentHtml);

                                            // If the new comment is the first one, show "Show More" link
                                            if (moreCommentsDiv.children().length > 1 && commentSection.find('.show-more-comments').length === 0) {
                                                commentSection.append(`<a href="#" class="show-more-comments" data-post-id="${postId}">Show More</a>`);
                                                commentSection.append(`<a href="#" class="show-less-comments" data-post-id="${postId}" style="display: none;">Show Less</a>`);
                                            }

                                            // Clear the comment textarea
                                            commentTextarea.val('');
                                        } else {
                                            alert("Failed to submit comment: " + response.error);
                                        }
                                    } catch (error) {
                                        console.error("Error parsing response. Raw response was:", result);
                                        alert("There was an error. Please try again later.");
                                    }
                                },
                                error: function () {
                                    alert("Error submitting comment.");
                                }
                            });
                        } else {
                            alert("Please enter a comment.");
                        }
                    });


                    // Handle like button click
                    $('#gridBlogs').on('click', '.like-button', function (e) {
                        e.preventDefault(); // Prevent page refresh if necessary

                        var postId = $(this).closest('.blog-card').data('post-id');
                        var likeButton = $(this);
                        console.log('POST for user: ', visitorid)
                        $.ajax({
                            url: "/Blog/db/request.php",
                            method: "POST",
                            data: {
                                "like_post": true,
                                "post_id": postId,
                                "user_id": userId, // userId passed from PHP
                                "visitor_id": visitorid
                            },
                            success: function (result) {
                                try {
                                    var response = JSON.parse(result);
                                    if (response.success) {
                                        // Update the like button with the new like count
                                        likeButton.text(`Like (${response.new_likes})`);
                                    } else if (response.success == 'new_likes') {
                                        alert("You have liked the post");
                                    }
                                    else {
                                        console.error("Error in liking post:", response.error);
                                        alert(response.error);  // Handle the "already liked" case
                                    }
                                } catch (error) {
                                    console.error("Error parsing response. Raw response was:", result);
                                    alert("There was an error. Please try again later.");
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error("AJAX error:", error);
                                alert("Error liking post.");
                            }
                        });
                    });


                    // Handle share button click
                    $('#gridBlogs').on('click', '.share-button', function () {
                        var postId = $(this).closest('.blog-card').data('post-id');
                        // var postTitle = $(this).closest('.blog-card').find('h3').text(); 
                        var baseUrl = window.location.origin; // Get the base URL dynamically
                        var shareUrl = `${baseUrl}/Blog/pages/blog.php?id=${postId}`;

                        // Share the blog (copy link to clipboard for example)
                        navigator.clipboard.writeText(shareUrl).then(function () {

                        }, function (err) {
                            alert('Failed to copy the link. Please try again.');
                        });
                    });

                } catch (error) {
                    console.error("Parsing error:", error);
                    console.error("Server response:", result);
                    alert("Failed to load data!");
                }
            },
            error: function () {
                alert("Something went wrong!");
            }
        });
    }
    // Function to load comments for a specific post
    function loadComments(postId) {
        console.log(`Attempting to load comments for post ID: ${postId}`);

        $.ajax({
            url: "/Blog/db/request.php",
            method: "POST",
            data: {
                load_comments: true,
                post_id: postId
            },
            success: function (result) {
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

                    let commentHtml = '';

                    if (comments.length === 0) {
                        commentHtml = '<p>No comments yet.</p>';
                    } else {
                        // Create a scrollable div for comments
                        commentHtml += `<div class="more-comments" style="max-height: 150px; overflow-y: auto;">`;

                        // Append all comments inside the scrollable area
                        comments.forEach(comment => {
                            commentHtml += `<p><strong>${comment.username}:</strong> ${comment.comment}</p>`;
                        });

                        commentHtml += `</div>`;

                        // Add "Show More" and "Show Less" links if there are more than 4 comments
                        if (comments.length > 4) {
                            commentHtml += `<a href="#" class="show-more-comments" data-post-id="${postId}">Show More</a>`;
                            commentHtml += `<a href="#" class="show-less-comments" data-post-id="${postId}" style="display: none;">Show Less</a>`;
                        }
                    }

                    // Append the comments to the correct element by post ID
                    $(`#comments-${postId}`).html(commentHtml);

                    // By default, hide comments beyond the first 4
                    $(`#comments-${postId} .more-comments p:gt(3)`).hide();
                } catch (error) {
                    console.error(`Error parsing JSON for post ID ${postId}:`, error);
                    console.error('Raw response:', result);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(`AJAX request failed: ${textStatus}`, errorThrown);
                alert('Failed to load comments.');
            }
        });
    }

    // Event listener for Show More functionality for comments
    $(document).on('click', '.show-more-comments', function (e) {
        e.preventDefault();
        var postId = $(this).data('post-id');
        $(`#comments-${postId} .more-comments p`).slideDown();
        $(this).hide(); // Hide "Show More"
        $(this).siblings('.show-less-comments').show(); // Show "Show Less"
    });

    $(document).on('click', '.show-less-comments', function (e) {
        e.preventDefault();
        var postId = $(this).data('post-id');
        $(`#comments-${postId} .more-comments p:gt(3)`).slideUp(); // Hide comments beyond the first 4
        $(this).hide(); // Hide "Show Less"
        $(this).siblings('.show-more-comments').show(); // Show "Show More"
    });




    // // Handle like button click
    // $('#gridBlogs').on('click', '.like-button', function () {
    //     alert('You liked this post!');
    //     // Implement like functionality
    // });

    // Handle share button click
    $('#gridBlogs').on('click', '.share-button', function () {
        alert('Copied the link for post!');
        // Implement share functionality
    });
});