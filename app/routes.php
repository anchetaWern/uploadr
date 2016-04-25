<?php
// Routes

$app->get('/', App\Action\HomeAction::class);

$app->post('/upload', App\Action\UploadAction::class);

$app->get('/files', App\Action\FileListAction::class);

$app->get('/download', App\Action\FileDownloadAction::class);