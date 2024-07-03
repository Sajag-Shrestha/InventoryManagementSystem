var BASE_URL = 'http://localhost/InventoryManagementSystem';
(function () {

  // Utility functions for selecting elements
  const select = (el, all = false) => {
    el = el.trim();
    if (all) {
      return [...document.querySelectorAll(el)];
    } else {
      return document.querySelector(el);
    }
  }

  const on = (type, el, listener, all = false) => {
    if (all) {
      select(el, all).forEach(e => e.addEventListener(type, listener));
    } else {
      select(el).addEventListener(type, listener);
    }
  }

  // Sidebar toggle functionality
  if (select('.toggle-sidebar-btn')) {
    on('click', '.toggle-sidebar-btn', function (e) {
      const body = select('body');
      const header = select('#header');
      const logoContainer = select('.logo-bg');
      const logoImg = select('.logo img');

      // Toggle the sidebar
      body.classList.toggle('toggle-sidebar');
      header.classList.toggle('header-sidebar-toggled');

      // Toggle the logo-bg styles and image source
      if (body.classList.contains('toggle-sidebar')) {
        logoContainer.classList.add('toggled');
        logoImg.src =  BASE_URL + '/assets/img/inventify-high-resolution-logo-purple-transparent.png'; // Path to the new image
      } else {
        logoContainer.classList.remove('toggled');
        logoImg.src = BASE_URL + '/assets/img/logo-no-background.png'; // Original image path
      }
    });
  }

  // Ensure jQuery is ready
  $(document).ready(function () {
    // Initialize Fancybox
    $('[data-fancybox="images"]').fancybox({
      buttons: [
        'zoom',
        'slideShow',
        'thumbs',
        'close'
      ],
      protect: true, // Prevent right-click and other interactions
      animationEffect: "zoom",
      transitionEffect: "circular",
      loop: true,
      thumbs: {
        axis: 'x' // Horizontal thumbnail gallery
    },
      afterClose: function (instance, slide) {
        // Scroll back to the original position after closing Fancybox
        $('html, body').animate({
          scrollTop: $(instance.current.opts.$orig).offset().top
        }, 500);
      }
    });

    // Image selection functionality
    const confirmButton = $("#confirmImageSelection");
    const imgLinkInput = $("#imgLink");
    const selectedImagePreview = $("#selectedImagePreview");
    const selectionMessage = $("#imageSelectionMessage");

    confirmButton.on("click", function () {
      console.log("Confirm button clicked");
      const selectedImage = $('input[name="selectedImage"]:checked');
      if (selectedImage.length > 0) {
        const selectedImageId = selectedImage.val();
        const selectedImageLink = selectedImage.data('img-link');
        console.log("Selected image ID:", selectedImageId);
        console.log("Selected image link:", selectedImageLink);
        imgLinkInput.val(selectedImageId);
        selectedImagePreview.html(`<img src="http://localhost/InventoryManagementSystem/uploads/${selectedImageLink}" class="img-thumbnail mt-2">`);
        $('#imageModal').modal('hide');

      
        setTimeout(() => {
          alertMessage.fadeOut("slow", () => {
            alertMessage.remove();
          });
        }, 2000);

        // Dismiss the modal (assuming Bootstrap modal)
        $("#yourModalId").modal('hide'); // Replace #yourModalId with your actual modal ID
      } else {
        console.log("No image selected");
      }
    });
  });

  

})();
