conferencer.tinymce_button({
	slug: 'sessions',
	title: "Sessions",
	image: 'sessions.png',
	onclick: function(editor) {
		editor.execCommand('mceInsertContent', 0, '[sessions]');
	}
});