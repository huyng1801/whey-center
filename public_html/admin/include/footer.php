</div> <!-- End of home-content -->
    </section> <!-- End of home-section -->
    <footer class="text-center py-3">
        <p>&copy; <?php echo date('Y'); ?> Admin Dashboard. Tất Cả Quyền Được Bảo Lưu.</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000); 
        });

        function toggleDropdown() {
            const profileDetails = document.querySelector('.profile-details');
            profileDetails.classList.toggle('active');
        }

        // Optional: Close the dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const profileDetails = document.querySelector('.profile-details');
            if (!profileDetails.contains(event.target)) {
                profileDetails.classList.remove('active');
            }
        });

        let sidebar = document.querySelector(".sidebar");
        let sidebarBtn = document.querySelector(".sidebarBtn");
        let logo_name = document.querySelector(".logo_name");
        sidebarBtn.onclick = function() {
            sidebar.classList.toggle("active");
            if(sidebar.classList.contains("active")){
                sidebarBtn.classList.replace("fa-bars", "fa-chevron-right");
                logo_name.classList.add("hidden");
            } else {
                sidebarBtn.classList.replace("fa-chevron-right", "fa-bars");
                logo_name.classList.remove("hidden");
            }
        }
    </script>
</body>
</html>
