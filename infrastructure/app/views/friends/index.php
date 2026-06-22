<?php 
    define("ASSET_URL", base_folder_path . "/public"); 
    include root_dir . "/app/views/partials/navbar.php"; 

    $currUserID = $currUserID ?? null;
    $displayUsers = $displayUsers ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Friends - C&B Hub</title>
    <style>
        .friends-container { max-width: 1000px; margin: 40px auto; padding: 0 20px; font-family: sans-serif; }
        
        /* Search Bar */
        .searching-section { 
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
        }
        
        .searching-bar {
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 750px;
            border: 1px solid #cbd5e1;
            border-radius: 50px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
        }

        .searching-input {
            border: none;
            outline: none;
            padding: 14px 20px;
            font-size: 16px;
            background: transparent;
        }

        .name-input {
            flex: 1;
        }

        .id-input {
            width: 150px;
            text-align: center;
        }

        .divider {
            width: 1px;
            height: 40px;
            background: #cbd5e1;
        }

        .searching-btn {
            border: none;
            padding: 14px 28px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            background: #2563eb;
            color: #fff;
            height: 100%;
            transition: background 0.2s ease;
        }

        .searching-btn:hover {
            background: #1d4ed8;
        }

        /* Section Headers */
        .section-title { font-size: 20px; color: #1e293b; margin-bottom: 16px; font-weight: 600; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;}

        /* Card Grid */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; margin-bottom: 40px; }
        
        /* Student Card */
        .student-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .student-card img { width: 72px; height: 72px; border-radius: 50%; margin-bottom: 12px; background: #f1f5f9; }
        .student-name { font-size: 18px; font-weight: 600; color: #0f172a; margin: 0 0 4px 0; }
        .student-context { font-size: 13px; color: #64748b; margin: 0 0 16px 0; display: flex; flex-direction: column; gap: 4px;}
        .context-highlight { color: #3b82f6; font-weight: 500; }

        /* Buttons */
        .btn-group { display: flex; gap: 8px; justify-content: center; }
        .btn { padding: 8px 16px; border-radius: 6px; font-weight: 500; font-size: 14px; cursor: pointer; border: none; flex: 1;}
        .btn-add { background: #3b82f6; color: white; }
        .btn-add:hover { background: #2563eb; }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-secondary:hover { background: #e2e8f0; }

        
    </style>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">  
</head>
<body style="background-color: #f8fafc; margin: 0;">

    <div class="friends-container">
        
        <div class="searching-section">
            <form action="<?= base_folder_path ?>/friends/search" method="GET" class="searching-bar">
                <input type="text" name="name" class="searching-input name-input" placeholder="Search by name">
                <div class="divider"></div>
                <input type="text" name="id" class="searching-input id-input" placeholder="Search by ID" />

                <button type="submit" class="searching-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <?php if (!empty($pendingRequests)): ?>
            
            <h2 class="section-title">Friend Requests (<?= count($pendingRequests) ?>)</h2>
            <div class="grid">
                <?php foreach($pendingRequests as $req): ?>
                    <div class="student-card">
                        <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?= htmlspecialchars($req['lastname']) ?>" alt="Avatar">
                        <h3 class="student-name"><?= htmlspecialchars($req['firstname'] . ' ' . $req['lastname']) ?></h3>
                        <p class="student-context">Wants to connect with you</p>
                        <div class="btn-group">
                            <form action="<?= base_folder_path ?>/friends/accept" method="POST" style="flex: 1;">
                                <input type="hidden" name="sender_id" value="<?= $req['ID'] ?>">
                                <button class="btn btn-add">Accept</button>
                            </form>
                            <form action="<?= base_folder_path ?>/friends/decline" method="POST" style="flex: 1;">
                                <input type="hidden" name="sender_id" value="<?= $req['ID'] ?>">
                                <button class="btn btn-secondary">Decline</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($isSearching) && $isSearching): ?>
            <h2 class="section-title">Search Results (<?= count($displayUsers) ?>)</h2>
        <?php else: ?>
            <h2 class="section-title">People You Might Know</h2>
        <?php endif; ?>


        <?php if (empty($displayUsers)): ?>
            <p style="text-align: center; color: #64748b; padding: 20px;">No people found.</p>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($displayUsers as $person): ?>
                    <?php
                        $student = $person["student"];
                        $studentProfile = $person["profile"];
                        $mutuals = $person["mutual_count"] ?? 0;
                        $sharedClubs = $person["shared_clubs"] ?? 0;
                        $sameMajor = $person["same_major"] ?? false; 
                        $friendshipRequest = $person["friendship"] ?? null;
                        // var_dump($friendshipRequest);
                        $senderID = $friendshipRequest ? $friendshipRequest->getSenderID() : '';
                        // echo "<div>Sender ID: {$senderID}</div>";
                        // echo "<div>{$friendshipRequest}</div>";
                        $isFriend = $person["is_friend"] ?? false;
                        $friendshipStatus = $person["friendship_status"] ?? null;
                        // if ($friendshipRequest && $friendshipStatus) echo "<div>{$friendshipStatus}</div>";
                    ?>

                    <div class="student-card">
                        <a href="<?= base_folder_path ?>/profile/view?id=<?= htmlspecialchars($student->getID()) ?>"
                            style="text-decoration: none; color: inherit; display: block;">
                            <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?= htmlspecialchars($student->getLastname()) ?>" alt="Avatar">
                            <h3 class="student-name"><?= htmlspecialchars($student->getFirstname() . ' ' . $student->getLastname()) ?></h3>
                        </a>
                        
                        <p class="student-context">
                            <?php if ($mutuals > 0): ?>
                                <span class="context-highlight"><?= $mutuals ?> mutual friend<?= $mutuals > 1 ? 's' : '' ?></span>
                            <?php endif; ?>

                            <?php if ($sharedClubs > 0): ?>
                                <span><?= $sharedClubs ?> shared club<?= $sharedClubs > 1 ? 's' : '' ?></span>
                            <?php endif; ?>

                            <?php if ($sameMajor): ?>
                                <span>Same major (<?= htmlspecialchars($studentProfile->getMajorAbbreviation()) ?>)</span>
                            <?php endif; ?>

                            <?php if ($mutuals === 0 && $sharedClubs === 0 && !$sameMajor): ?>
                                <span>No mutual connections</span>
                            <?php endif; ?>
                        </p>
                        
                        <?php if ($friendshipStatus === "pending" && $senderID !== '' && $senderID !== $_SESSION["user_ID"]): ?>
                            <!-- <div>The current user ID -> <?= $_SESSION["user_ID"] ?> isn't the same with sender ID <?= $senderID ?></div> -->
                            <div style="display: flex; gap: 0.5rem; width: 100%; margin-top: 10px;">
                                <form action="<?= base_folder_path ?>/friends/accept" method="POST" style="flex: 1; margin: 0;">
                                    <input type="hidden" name="sender_ID" value="<?= htmlspecialchars($student->getID()) ?>">
                                    <button class="btn btn-add" style="cursor: pointer;">Accept</button>
                                </form>
                            
                                <form action="<?= base_folder_path ?>/friends/reject" method="POST" style="flex: 1; margin: 0;">
                                    <input type="hidden" name="sender_ID" value="<?= htmlspecialchars($student->getID()) ?>">
                                    <button class="btn btn-secondary" style="cursor: pointer;">Decline</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <?php if ($friendshipStatus === "pending" && $senderID === $_SESSION["user_ID"]): ?>
                                <div class="friend-dropdown-toggle" style="position: relative; width: 100%;">
                                    <button class="btn btn-add" style="width: 100%; background-color: #706D6D; color: #fff;">
                                        Sent a request ▾
                                    </button>
                                    <div class="friend-dropdown-menu" style="
                                        display: none;
                                        position: absolute;
                                        bottom: calc(100% + 6px);
                                        left: 0;
                                        width: 100%;
                                        background: #fff;
                                        border: 1px solid #e2e8f0;
                                        border-radius: 8px;
                                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                                        overflow: hidden;
                                        z-index: 100;
                                        animation: dropUp 0.2 ease;
                                    ">
                                        <form action="<?= base_folder_path?>/friends/withdrawRequest" method="POST">
                                            <input type="hidden" name="receiver_ID" value="<?= htmlspecialchars($student->getID()) ?>">
                                            <button type="submit"
                                                style="width: 100%; padding: 10px 16px; background: none; border: none;
                                                        color: #f59e0b; font-weight: 600; font-size: 14px; cursor: pointer;
                                                        text-align: left; display: flex; align-items: center; gap: 8px;"
                                                onmouseover="this.style.background='#fffbeb'"
                                                onmouseout="this.style.background='none'"
                                                onclick="return confirm('Withdraw this friend request?')">
                                                ↩ Withdraw Request
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php elseif ($friendshipStatus === "accepted"): ?>
                                <div class="friend-dropdown-toggle" style="position: relative; width: 100%;">
                                    <button class="btn btn-add" style="width: 100%; background-color: #10b981; color: #fff;"> ✓ Friends ▾</button>
                                    <div class="friend-dropdown-menu" style="
                                        display: none;
                                        position: absolute;
                                        bottom: calc(100% + 6px);
                                        left: 0;
                                        width: 100%;
                                        background: #fff;
                                        border: 1px solid #e2e8f0;
                                        border-radius: 8px;
                                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                                        overflow: hidden;
                                        z-index: 100;
                                        animation: dropUp 0.2s ease;
                                    ">
                                        <form action="<?= base_folder_path ?>/friends/unfriend" method="POST">
                                            <input type="hidden" name="friend_ID" value="<?= htmlspecialchars($student->getID()) ?>">
                                                <button type="submit"
                                                        style="width: 100%; padding: 10px 16px; background: none; border: none;
                                                        color: #ef4444; font-weight: 600; font-size: 14px; cursor: pointer;
                                                        text-align: left; display: flex; align-items: center; gap: 8px;"
                                                        onmouseover="this.style.background='#fef2f2'"
                                                        onmouseout="this.style.background='none'"
                                                        onclick="return confirm('Remove this friend?')">
                                                    🗑 Unfriend
                                                </button>
                                        </form>
                                    </div>
                                </div>

                            <?php else: ?>
                                <form action="<?= base_folder_path ?>/friends/add" method="POST" style="width: 100%; margin-top: 10px;">
                                    <input type="hidden" name="receiver_ID" value="<?= htmlspecialchars($student->getID()) ?>" />
                                    <button class="btn btn-add" style="width: 100%;">Add Friend</button>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- <form action="<?= base_folder_path ?>/friends/add" method="POST">
                            <input type="hidden" name="receiver_ID" value="<?= htmlspecialchars($student->getID()) ?>">
                            <?php if (!$isFriend): ?>
                                <button class="btn btn-add" style="width: 100%;">Add</button>
                            <?php elseif ($friendshipRequest && $friendshipStatus === "pending"): ?>    
                                <button class="btn btn-add" style="width: 100%; background-color: #706D6D;" id="<?= $friendshipStatus ?>" disabled>Sent a request</button>
                            <?php elseif ($friendshipRequest && $friendshipStatus === "accepted"): ?>
                                <button class="btn btn-add" style="width: 100%; background-color: #ADA8A8;" id="<?= $friendshipStatus ?>" disabled>Accepted</button>
                            <?php endif; ?>
                        </form> -->
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <style>
        @keyframes dropUp {
            from {
                opacity: 0; 
                transform: translateY(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Toggle dropdown on button click
            document.querySelectorAll(".friend-dropdown-toggle").forEach((wrapper) => {
                console.log(`Clicked on the accpeted button to see unfriend option!`);
                const btn = wrapper.querySelector("button");
                const menu = wrapper.querySelector(".friend-dropdown-menu");

                btn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    const isOpen = menu.style.display === "block";

                    // Close all other open dropdowns first
                    document.querySelectorAll(".friend-dropdown-menu").forEach(m => m.style.display = "none");

                    menu.style.display = isOpen ? "none" : "block";
                });
            });

            // Click anywhere outside to close all dropdowns
            document.addEventListener("click", () => {
                document.querySelectorAll(".friend-dropdown-menu").forEach(m => m.style.display = "none");
            });
        });
    </script>
</body>
</html>