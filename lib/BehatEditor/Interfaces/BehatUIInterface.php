<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 5/20/14
 * Time: 12:32 PM
 */

namespace BehatEditor\Interfaces;


interface BehatUIInterface {


    public function show($path_filename);
    public function edit($path_filename, $content, $message, $sha, $branch);
    public function update($path_filename, $content, $message, $sha, $branch);
    public function create($path_filename, $content, $message, $branch);
    public function run();
    public function index($path);
    public function content($path);
    public function delete($path, $message, $sha);
}