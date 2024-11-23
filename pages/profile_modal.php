<div id="profileModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeProfileModal()">&times;</span>
    <div class="profile-info">
      <img id="profilePicture" src="/Blog/assets/default-profile.png" alt="Profile Picture" />
      <h2 id="profileName">User Name</h2>
      <p id="profileEmail">Email: user@example.com</p>
      <p id="profileRole">Role: User</p>
      <div class="profile-actions">
        <button onclick="editProfile()">Edit Profile</button>
        <button onclick="viewDetails()">View Details</button>
      </div>
    </div>
  </div>
</div>

<script>
  function showProfileModal() {
    const profileData = {
      name: "<?php echo $_SESSION['username'] ?? 'Guest'; ?>",
      email: "<?php echo $_SESSION['email'] ?? 'N/A'; ?>",
      role: "<?php echo $_SESSION['role'] ?? 'User'; ?>",
      picture: "<?php echo $_SESSION['profile_picture'] ?? '/Blog/assets/default-profile.png'; ?>",
    };

    document.getElementById('profileName').textContent = profileData.name;
    document.getElementById('profileEmail').textContent = `Email: ${profileData.email}`;
    document.getElementById('profileRole').textContent = `Role: ${profileData.role}`;
    document.getElementById('profilePicture').src = profileData.picture;

    document.getElementById('profileModal').style.display = 'block';
  }

  function closeProfileModal() {
    document.getElementById('profileModal').style.display = 'none';
  }

  function editProfile() {
    alert('Edit Profile functionality is under development.');
  }

  function viewDetails() {
    alert('View Details functionality is under development.');
  }
</script>

<style>
  .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.6);
  }

  .modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    width: 400px;
    border-radius: 10px;
    text-align: center;
    animation: slideDown 0.3s ease-in-out;
  }

  @keyframes slideDown {
    from {
      transform: translateY(-100%);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  .profile-info img {
    border-radius: 50%;
    width: 120px;
    height: 120px;
    margin-bottom: 15px;
  }

  .profile-actions {
    margin-top: 15px;
  }

  .profile-actions button {
    margin: 5px;
    padding: 10px 20px;
    border: none;
    background-color: #007BFF;
    color: white;
    cursor: pointer;
    border-radius: 5px;
  }

  .profile-actions button:hover {
    background-color: #0056b3;
  }
</style>
