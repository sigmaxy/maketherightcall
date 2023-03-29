jQuery(document).ready(function($){
    window.onunload = refreshParent;
    function refreshParent() {
        window.opener.location.reload();
    }
})