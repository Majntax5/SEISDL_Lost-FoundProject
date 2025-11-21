<!-- inside the Create Post form in dashboard.php: -->
<label class="block mb-2">Image
  <input type="file" name="image" accept="image/*" id="imageInput" class="w-full p-2">
</label>
<!-- preview -->
<img id="imgPreview" class="hidden w-full h-48 object-cover rounded mt-2" src="" alt="Preview">

<script>
document.getElementById('imageInput').addEventListener('change', function(e){
  const file = e.target.files[0];
  const img = document.getElementById('imgPreview');
  if (!file) { img.src = ''; img.classList.add('hidden'); return; }
  const reader = new FileReader();
  reader.onload = function(ev){
    img.src = ev.target.result;
    img.classList.remove('hidden');
  };
  reader.readAsDataURL(file);
});
</script>