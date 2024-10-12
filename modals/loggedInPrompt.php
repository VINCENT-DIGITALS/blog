<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        margin: 15% auto;
        padding: 20px;
        background-color: white;
        border-radius: 5px;
        width: 300px;
        text-align: center;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
<!-- Modal for Login Prompt -->
<div id="loginModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>You need to log in to continue.</p>
        <button id="loginBtn" class="btn btn-primary">Login</button>
        <button id="cancelBtn" class="btn btn-secondary">Cancel</button>
    </div>
</div>