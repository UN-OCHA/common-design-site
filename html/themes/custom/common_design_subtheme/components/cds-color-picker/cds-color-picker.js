(function iife() {
  // Locate the required DOM elements for this component.
  var colorPicker = document.querySelector('.cds-color-picker');
  var root = document.documentElement;

  // We only proceed if the color picker is found.
  if (!colorPicker) {
    console.log('cds-color-picker: could not find a color picker.');
    return;
  }

  // Find all the colors defined in markup
  var palettes = document.querySelectorAll('.cdscp-palette');

  // Process each color palette.
  palettes.forEach((p) => {
    // Set up an event listener to re-color the site when clicked.
    p.querySelector('button').addEventListener('click', (ev) => {
      root.style.setProperty('--brand-primary', p.dataset.brandPrimary);
      root.style.setProperty('--brand-primary--light', p.dataset.brandPrimaryLight);
      root.style.setProperty('--brand-primary--dark', p.dataset.brandPrimaryDark);
      root.style.setProperty('--brand-highlight', p.dataset.brandHighlight);
      root.style.setProperty('--brand-grey', p.dataset.brandGrey);
    });
  })
}());
