<aside id="menubar" class="menubar light">
    <div class="menubar-scroll">
        <div class="menubar-scroll-inner">
            <ul class="app-menu">
                <li>
                    <a href="dashboard.php" class="menu-link">
                        <i class="bi bi-speedometer2 menu-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-header">Appointments</li>

                <li>
                    <a href="new-appointment.php" class="menu-link">
                        <i class="bi bi-journal-plus menu-icon"></i>
                        <span>New</span>
                        <?php
                        $docid=$_SESSION['damsid'];
                        $sql ="SELECT ID from tblappointment where Status is null && Doctor=:docid";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':docid', $docid, PDO::PARAM_STR);
                        $query->execute();
                        if($query->rowCount() > 0) {
                            echo '<span class="badge bg-warning rounded-pill ms-auto">'.$query->rowCount().'</span>';
                        }
                        ?>
                    </a>
                </li>

                <li>
                    <a href="approved-appointment.php" class="menu-link">
                        <i class="bi bi-check-circle menu-icon"></i>
                        <span>Approved</span>
                    </a>
                </li>

                <li>
                    <a href="cancelled-appointment.php" class="menu-link">
                        <i class="bi bi-x-circle menu-icon"></i>
                        <span>Cancelled</span>
                    </a>
                </li>

                <li>
                    <a href="all-appointment.php" class="menu-link">
                        <i class="bi bi-calendar-week menu-icon"></i>
                        <span>All Appointments</span>
                    </a>
                </li>

                <li class="menu-header">Schedule</li>

                <li>
                    <a href="manage-availability.php" class="menu-link">
                        <i class="bi bi-clock menu-icon"></i>
                        <span>My Availability</span>
                    </a>
                </li>

                <li>
                    <a href="waitlist.php" class="menu-link">
                        <i class="bi bi-hourglass-split menu-icon"></i>
                        <span>Waitlist</span>
                        <?php
                        $sql ="SELECT ID from tblwaitlist where Status='Waiting' && Doctor=:docid";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':docid', $docid, PDO::PARAM_STR);
                        $query->execute();
                        if($query->rowCount() > 0) {
                            echo '<span class="badge bg-info rounded-pill ms-auto">'.$query->rowCount().'</span>';
                        }
                        ?>
                    </a>
                </li>

                <li class="menu-header">Tools</li>

                <li>
                    <a href="search.php" class="menu-link">
                        <i class="bi bi-search menu-icon"></i>
                        <span>Search Records</span>
                    </a>
                </li>

                <li>
                    <a href="appointment-bwdates.php" class="menu-link">
                        <i class="bi bi-file-earmark-text menu-icon"></i>
                        <span>Reports</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>

<style>
.menubar {
    background: #fff;
    width: 250px;
    position: fixed;
    top: 60px;
    bottom: 0;
    left: 0;
    z-index: 100;
    box-shadow: 1px 0 2px rgba(0,0,0,.05);
}

.menubar-scroll {
    height: 100%;
    overflow-y: auto;
    padding: 1.5rem 0;
}

.app-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-header {
    padding: 1.2rem 1.5rem 0.5rem;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #6c757d;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.menu-link {
    display: flex;
    align-items: center;
    padding: 0.6rem 1.5rem;
    color: #495057;
    text-decoration: none;
    transition: all 0.2s;
}

.menu-link:hover,
.menu-link.active {
    color: var(--bs-primary);
    background: var(--bs-light);
}

.menu-icon {
    width: 1.5rem;
    font-size: 1.1rem;
    margin-right: 0.75rem;
    text-align: center;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Custom Scrollbar */
.menubar-scroll::-webkit-scrollbar {
    width: 4px;
}

.menubar-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.menubar-scroll::-webkit-scrollbar-thumb {
    background: #e9ecef;
    border-radius: 4px;
}

.menubar-scroll::-webkit-scrollbar-thumb:hover {
    background: #dee2e6;
}

/* Adjust main content area */
.app-main {
    margin-left: 250px;
    margin-top: 60px;
    min-height: calc(100vh - 60px);
    background: #f8f9fa;
    transition: margin 0.3s;
}

@media (max-width: 992px) {
    .menubar {
        transform: translateX(-100%);
        transition: transform 0.3s;
    }

    .menubar.show {
        transform: translateX(0);
    }

    .app-main {
        margin-left: 0;
    }
}
</style>