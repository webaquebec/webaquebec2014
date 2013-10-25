conferencer.tinymce_button({
	slug: 'session_meta',
	title: "Session Meta",
	image: 'session_meta.png',
	onclick: function(editor) {
		editor.execCommand('mceInsertContent', 0, '[session_meta]');
	}
});