<?php


require_once __DIR__ . '/../../config/config.php';
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <h2 class="text-2xl font-semibold leading-tight">Backup & Restore</h2>
        <p class="mt-2 text-gray-600">Create backups of your database.</p>

        <div class="mt-8 p-6 bg-white rounded-lg shadow max-w-md">
            <h3 class="text-lg font-semibold mb-4">Database Backup</h3>
            <p class="text-gray-600 mb-4">
                Click the button below to download a full backup of your site's database as a <code>.sql</code> file. 
                Keep this file in a safe place.
            </p>
            <a href="util/create_backup.php" 
               class="w-full text-center bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline inline-block">
                Download Database Backup
            </a>
        </div>

        <div class="mt-8 p-6 bg-white rounded-lg shadow max-w-md">
            <h3 class="text-lg font-semibold mb-4">Restore</h3>
            <div class="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                <p class="font-bold">Manual Restore Required</p>
                <p>Restoring from a backup must be done manually using a database management tool like phpMyAdmin or the command line to prevent accidental data loss.</p>
            </div>
        </div>
    </div>
</div>
