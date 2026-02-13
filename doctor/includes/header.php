<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top">
    <div class="container-fluid px-4">
        <button type="button" class="btn btn-link text-dark p-0 me-3" id="menubar-toggle-btn">
            <i class="bi bi-list fs-4"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <i class="bi bi-heart-pulse text-primary me-2"></i>
            <span class="fw-semibold">DAMS</span>
        </a>

        <div class="d-flex align-items-center ms-auto">
            <!-- Quick Actions -->
            <div class="dropdown d-inline-block me-3">
                <button class="btn btn-light border-0" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-plus-lg"></i>
                    <span class="d-none d-md-inline ms-1">Quick Actions</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="new-appointment.php"><i class="bi bi-calendar-plus me-2"></i>New Appointment</a></li>
                    <li><a class="dropdown-item" href="manage-availability.php"><i class="bi bi-clock me-2"></i>Set Availability</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="search.php"><i class="bi bi-search me-2"></i>Search Records</a></li>
                </ul>
            </div>

            <!-- Notifications -->
            <div class="dropdown d-inline-block me-3">
                <button class="btn btn-light position-relative border-0" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    <?php
                    $docid = $_SESSION['damsid'];
                    $sql = "SELECT ID from tblappointment where Status is null && Doctor=:docid";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':docid', $docid, PDO::PARAM_STR);
                    $query->execute();
                    $newCount = $query->rowCount();
                    if($newCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo $newCount; ?>
                    </span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-sm pt-0" style="width: 300px;">
                    <div class="p-2 border-bottom">
                        <h6 class="mb-0">Notifications</h6>
                    </div>
                    <div class="p-2">
                        <?php
                        $sql = "SELECT * from tblappointment where Status is null && Doctor=:docid ORDER BY ID DESC LIMIT 5";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':docid', $docid, PDO::PARAM_STR);
                        $query->execute();
                        $notifications = $query->fetchAll(PDO::FETCH_OBJ);
                        
                        if($query->rowCount() > 0):
                            foreach($notifications as $notification): ?>
                            <a href="view-appointment-detail.php?editid=<?php echo $notification->ID; ?>&&aptid=<?php echo $notification->AppointmentNumber; ?>" 
                               class="dropdown-item d-flex align-items-center py-2 px-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-light rounded-circle">
                                        <i class="bi bi-person text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-0 fs-sm"><?php echo $notification->Name; ?></p>
                                    <small class="text-muted"><?php echo $notification->AppointmentNumber; ?></small>
                                </div>
                            </a>
                            <?php endforeach;
                        else: ?>
                            <p class="text-muted small mb-0">No new notifications</p>
                        <?php endif; ?>
                    </div>
                    <?php if($newCount > 5): ?>
                    <div class="p-2 border-top">
                        <a href="new-appointment.php" class="btn btn-sm btn-light d-block">View All</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Menu -->
            <div class="dropdown d-inline-block">
                <button class="btn btn-light d-flex align-items-center border-0" type="button" data-bs-toggle="dropdown">
                    <div class="avatar avatar-sm bg-light rounded-circle">
                        <img src="assets/images/images.png" class="rounded-circle" width="32" height="32" alt="avatar">
                    </div>
                    <span class="d-none d-md-inline ms-2">Dr. <?php echo $fname; ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>My Profile</a></li>
                    <li><a class="dropdown-item" href="change-password.php"><i class="bi bi-shield-lock me-2"></i>Change Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<style>
.navbar {
    height: 60px;
}
.avatar {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.dropdown-item {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}
.dropdown-item:hover {
    background-color: var(--bs-light);
}
.btn-light {
    background-color: var(--bs-light);
}
.btn-light:hover {
    background-color: var(--bs-light);
    opacity: 0.9;
}
</style>