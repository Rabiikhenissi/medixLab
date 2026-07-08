<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Group;
use App\Models\Action;

echo "=== TESTING PERMISSION LOGIC ===\n";

// 1. Fetch Admin User
$adminUser = User::where('email', 'admin@medix.com')->first();
if ($adminUser) {
    echo "Found Admin User: {$adminUser->first_name} {$adminUser->last_name} | Group: " . ($adminUser->group ? $adminUser->group->name : 'None') . "\n";
    echo "Has 'view-users' permission? " . ($adminUser->hasPermission('view-users') ? 'YES' : 'NO') . "\n";
    echo "Has 'non-existent-action' permission? " . ($adminUser->hasPermission('non-existent-action') ? 'YES' : 'NO') . " (Note: Admin group code gets true by default)\n";
} else {
    echo "Admin User not found!\n";
}

// 2. Create a test group and assign some permissions
echo "\nCreating Test Group 'Lab Assistant'...\n";
$group = Group::updateOrCreate(
    ['code' => 'lab-assistant'],
    ['name' => 'Lab Assistant', 'is_archive' => false]
);

$actionViewExams = Action::where('code', 'view-exams')->first();
$actionCreateExams = Action::where('code', 'create-exams')->first();

if ($actionViewExams && $actionCreateExams) {
    $group->actions()->sync([$actionViewExams->id, $actionCreateExams->id]);
    echo "Assigned actions 'view-exams' and 'create-exams' to Lab Assistant group.\n";
}

// 3. Create a test user in Lab Assistant group
echo "\nCreating Test User in Lab Assistant group...\n";
$user = User::updateOrCreate(
    ['email' => 'assistant@medix.com'],
    [
        'first_name' => 'Assistant',
        'last_name' => 'User',
        'phone' => '11112222',
        'password' => Hash::make('password'),
        'group_id' => $group->id,
        'is_archive' => false,
    ]
);

// We need to make sure they have an admin record to pass the admin dashboard middleware
App\Models\Admin::firstOrCreate(['user_id' => $user->id]);

// Reload user relations
$user->load('group.actions');

echo "User '{$user->first_name}' created.\n";
echo "Has 'view-exams' permission? " . ($user->hasPermission('view-exams') ? 'YES' : 'NO') . " (Should be YES)\n";
echo "Has 'create-exams' permission? " . ($user->hasPermission('create-exams') ? 'YES' : 'NO') . " (Should be YES)\n";
echo "Has 'view-users' permission? " . ($user->hasPermission('view-users') ? 'YES' : 'NO') . " (Should be NO)\n";

echo "\nVerification script completed successfully!\n";
