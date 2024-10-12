<?php
require_once __DIR__ . '../../../db/session_manager.php';
requireAdmin();
require_once '../../db/db.php';

$mydb = new myDB;
$blogs = $mydb->select('posts');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        header {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        header button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        main {
            padding: 20px;
        }

        /* Grid Layout */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .blog-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            position: relative;
        }

        .blog-card h3 {
            margin-top: 0;
        }

        .blog-card p {
            color: #666;
        }

        .blog-card button {
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            cursor: pointer;
            color: white;
        }

        .updateBtn {
            background-color: #007BFF;
        }

        .deleteBtn {
            background-color: #DC3545;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content h2 {
            margin-top: 0;
        }

        .modal-content form input,
        .modal-content form textarea {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .modal-content textarea {
            width: 100%;
            height: 150px;
            resize: vertical;
            box-sizing: border-box;
        }

        .modal-content button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        .modal-content .cancelBtn {
            background-color: #f44336;
            margin-left: 10px;
        }

        .search-container {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
            width: 100%;
            padding-top: 10px;
        }

        .search-container input[type="text"] {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-container input[type="text"]:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
        }
    </style>
</head>

<body>
    <?php include '../../includes/nav_bar.php'; ?>

    <header>
        <h1>My Blogs</h1>
        <button id="createBlogBtn">+ Create New Blog</button>
    </header>
    <section class="search-container">
        <input type="text" id="searchInput" placeholder="Search for a blog..." />
    </section>
    <main>
        <!-- Create Blog Modal -->
        <div id="createBlogModal" class="modal" style="display: none;">
            <div class="modal-content">
                <h2>Create Blog</h2>
                <form id="createBlogForm">
                    <label>Title:</label>
                    <input type="text" name="title" required>

                    <label>Content:</label>
                    <textarea name="content" rows="5" required></textarea>
                    <button type="submit">Create</button>
                    <button type="button" class="cancelBtn" onclick="closeModal('#createBlogModal')">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Blog List -->
        <section>

            <div class="grid-container" id="gridBlogs">
                <!-- Blog cards will be populated here by JavaScript -->
            </div>
        </section>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script>
        // Show Create Blog Modal
        $('#createBlogBtn').on('click', function() {
            $('#createBlogModal').show();
        });

        // Close Modal function
        function closeModal(modalId) {
            $(modalId).hide();
        }

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
                                <div class="blog-card">
                                    <h3>${data['title']}</h3>
                                    <p><strong>Author:</strong> ADMIN</p>
                                    <p>${contentToShow}</p>
                                    <p><strong>Date:</strong> ${data['created_at']}</p>
                                    <button class="updateBtn" data-id="${data['id']}">Update</button>
                                    <button class="deleteBtn" data-id="${data['id']}">Delete</button>
                                </div>`;
                                });
                            }
                            $('#gridBlogs').html(grid);

                            // Event listeners for update, delete buttons
                            $(".updateBtn").on('click', function() {
                                var blogId = $(this).data('id');
                                openUpdateModal(blogId);
                            });
                            $(".deleteBtn").on('click', function() {
                                var blogId = $(this).data('id');
                                deleteBlog(blogId);
                            });

                            // Single event listener for toggle content
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

        });
        // Create Blog
        $('#createBlogForm').on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: "../../db/request.php",
                method: "POST",
                data: $(this).serialize() + "&create_blog=true",
                success: function(response) {
                    if (response == "success") {
                        alert("Blog created successfully!");
                        closeModal('#createBlogModal');
                        loadBlogs();
                    } else {
                        alert("Failed to create blog.");
                    }
                }
            });
        });

        // Delete Blog
        function deleteBlog(blogId) {
            if (confirm("Are you sure you want to delete this blog?")) {
                $.ajax({
                    url: "../../db/request.php",
                    method: "POST",
                    data: {
                        "delete_blog": true,
                        "id": blogId
                    },
                    success: function(response) {
                        if (response == "success") {
                            alert("Blog deleted successfully!");
                            loadBlogs();
                        } else {
                            alert("Failed to delete blog.");
                        }
                    }
                });
            }
        }

        // Update Blog - Open Update Modal and Handle Update
        function openUpdateModal(blogId) {
            // Similar to create modal; fetch and populate blog data, then handle update via AJAX
        }
    </script>
</body>

</html>