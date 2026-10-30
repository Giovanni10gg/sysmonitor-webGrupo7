<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class StorageController extends Controller
{
public function index()
{
$disksData = json_decode(shell_exec('lsblk -J -o NAME,SIZE,FSTYPE,MOUNTPOINT,TYPE'), true)['blockdevices'] ?? [];
$cmd = "df -h --output=source,fstype,size,used,avail,pcent,target | grep -E '^/dev/'";
$lines = array_filter(explode("\n", trim(shell_exec($cmd))));
$filesystems = array_map(function($line)  {
return array_combine(['filesystem','type','size','used','avail','percentage','mountpoint'], preg_split("/\s+/", trim($line)));
}, $lines);

return view('storage.index', compact('disksData', 'filesystems'));
}
}
