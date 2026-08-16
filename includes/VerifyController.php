<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function checkAdmin() {
    if(!isLoggedIn() || $_SESSION['role']!=='admin'){
        header("Location: ../views/login.php?error=unauthorized");
        exit();
    }
}

function checkMember() {
    if(!isLoggedIn() || $_SESSION['role']!=='member'){
        header("Location: ../views/login.php?error=unauthorized");
        exit();
    }
}

function checkClient() {
    if(!isLoggedIn() || $_SESSION['role']!=='client'){
        header("Location: ../views/login.php?error=unauthorized");
        exit();
    }
}

function checkTeamLead() {
    if(!isLoggedIn() || $_SESSION['role']!=='team_lead'){
        header("Location: ../views/login.php?error=unauthorized");
        exit();
    }
}