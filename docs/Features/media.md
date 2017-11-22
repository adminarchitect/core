## File Manager

![Admin Architect - Media](http://docs.adminarchitect.com/images/plugins/media.png)

Very often you need the way to upload & attach various files to your web pages.
For these purposes Admin Architect comes with a `File Manager` tool.

To activate the `File Manager`, just set the `'file_manager' => true` in the `config/administrator.php`,
also you're able to configure the main storage by setting `paths.media` option.

`File Manager` allows you to create/rename/delete files & folders, upload/preview/download files.

### Bonus
`File Manager`, `TinyMCE Image` & `Eloquent MediaLibrary` are connected altogether. 
So any image uploaded from `Media Library` or `TinyMCE Image`, will be available in `File Manager`  