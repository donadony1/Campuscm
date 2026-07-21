$(function () {

  // Prévisualisation d'image avant upload (logo, cover, photos)
  $(document).on('change', 'input[type=file][data-preview]', function () {
    const target = $(this).data('preview');
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
      $(target).attr('src', e.target.result).removeClass('d-none');
    };
    reader.readAsDataURL(file);
  });

  // Confirmation avant suppression (filières, photos, écoles...)
  $(document).on('click', '[data-confirm]', function (e) {
    const msg = $(this).data('confirm') || 'Confirmez-vous cette action ?';
    if (!confirm(msg)) {
      e.preventDefault();
      return false;
    }
  });

  // Suppression AJAX d'une photo de galerie (admin)
  $(document).on('click', '.delete-photo-btn', function (e) {
    e.preventDefault();
    if (!confirm('Supprimer cette photo ?')) return;

    const $card = $(this).closest('.photo-item');
    const photoId = $(this).data('id');
    const token = $('meta[name="csrf-token"]').attr('content') || $('#csrf_token_val').val();

    $.post('actions.php', { action: 'delete_photo', id: photoId, csrf_token: token }, function (resp) {
      if (resp && resp.success) {
        $card.fadeOut(200, function () { $(this).remove(); });
      } else {
        alert((resp && resp.message) || 'Erreur lors de la suppression.');
      }
    }, 'json').fail(function () {
      alert('Erreur réseau lors de la suppression.');
    });
  });

  // Auto-dismiss des alertes flash après 5s
  setTimeout(function () {
    $('.alert-dismissible').fadeOut(400);
  }, 5000);

});
