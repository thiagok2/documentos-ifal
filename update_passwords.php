<?php

$users = App\Models\User::all();
$count = 0;
foreach($users as $user) {
    if (Hash::check('123456', $user->password)) {
        $user->password = Hash::make('admin@123456#7');
        $user->save();
        $count++;
    }
}
echo "Updated $count users.\\n";
