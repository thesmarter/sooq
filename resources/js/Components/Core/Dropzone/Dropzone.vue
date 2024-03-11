<script setup>
import {ref,onMounted} from 'vue'
import { Head, useForm,usePage } from "@inertiajs/vue3";
import Dropzone from "dropzone";
import "dropzone/dist/dropzone.css";

const props = defineProps({
  images: Object,
  edit_mode:{
    type:Number,
    default:0,
  },
  item_id:{
    type:Number,
    default:0,
  },
  max_image_upload:{
    type:Number,
  },
  dir: String
})
const caption=ref();


const dropRef = ref(null)
const emit = defineEmits(['clicked']);
const customPreview = `<div class="dz-preview dz-file-preview ">
  <div class="dz-image flex justify-center items-center"><img data-dz-thumbnail /></div>
  <div class="dz-details">
    <div class="dz-size"><span data-dz-size></span></div>
    <div class="dz-filename"><span data-dz-name></span></div>
  </div>
  <div class="dz-progress">
    <span class="dz-upload" data-dz-uploadprogress></span>
  </div>
  <div class="dz-error-message"><span data-dz-errormessage></span></div>
  <div class="dz-success-mark">
    <svg
      width="54"
      height="54"
      viewBox="0 0 54 54"
      fill="white"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M10.2071 29.7929L14.2929 25.7071C14.6834 25.3166 15.3166 25.3166 15.7071 25.7071L21.2929 31.2929C21.6834 31.6834 22.3166 31.6834 22.7071 31.2929L38.2929 15.7071C38.6834 15.3166 39.3166 15.3166 39.7071 15.7071L43.7929 19.7929C44.1834 20.1834 44.1834 20.8166 43.7929 21.2071L22.7071 42.2929C22.3166 42.6834 21.6834 42.6834 21.2929 42.2929L10.2071 31.2071C9.81658 30.8166 9.81658 30.1834 10.2071 29.7929Z"
      />
    </svg>
  </div>
  <div class="dz-error-mark">
    <svg
      width="54"
      height="54"
      viewBox="0 0 54 54"
      fill="white"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M26.2929 20.2929L19.2071 13.2071C18.8166 12.8166 18.1834 12.8166 17.7929 13.2071L13.2071 17.7929C12.8166 18.1834 12.8166 18.8166 13.2071 19.2071L20.2929 26.2929C20.6834 26.6834 20.6834 27.3166 20.2929 27.7071L13.2071 34.7929C12.8166 35.1834 12.8166 35.8166 13.2071 36.2071L17.7929 40.7929C18.1834 41.1834 18.8166 41.1834 19.2071 40.7929L26.2929 33.7071C26.6834 33.3166 27.3166 33.3166 27.7071 33.7071L34.7929 40.7929C35.1834 41.1834 35.8166 41.1834 36.2071 40.7929L40.7929 36.2071C41.1834 35.8166 41.1834 35.1834 40.7929 34.7929L33.7071 27.7071C33.3166 27.3166 33.3166 26.6834 33.7071 26.2929L40.7929 19.2071C41.1834 18.8166 41.1834 18.1834 40.7929 17.7929L36.2071 13.2071C35.8166 12.8166 35.1834 12.8166 34.7929 13.2071L27.7071 20.2929C27.3166 20.6834 26.6834 20.6834 26.2929 20.2929Z"
      />
    </svg>
  </div>
</div>`

onMounted(function(e){
    if(dropRef.value !==null){
        // Dropzone.autoDiscover = false;
        let sub_domain_url = '';
        let clearSlash =  props.dir;
        let subFolder = clearSlash.replaceAll("\\", "");
        
        if(import.meta.env.PROD){

            if(subFolder != null && subFolder != '')
            {
                sub_domain_url = '/'  + props.dir;
            }
            else
            {
                sub_domain_url = '';
            }


        }else{
            sub_domain_url = '';
        }
            let myDropzone = new Dropzone(dropRef.value,{
              previewTemplate:customPreview,

              url: sub_domain_url + '/admin/item/upload-multi',
              method:'Post',
              maxFiles: props.max_image_upload,

              headers: {
                              'X-CSRF-TOKEN':  usePage().props.csrf,
                          },
              acceptedFiles: "image/jpeg,image/png,image/jpg",
              addRemoveLinks: true,

              init: function (file) {

              this.on("thumbnail", function(file, dataUrl) {
                // console.log(dataUrl)
                var node = file.previewElement.childNodes[1].childNodes[0];
                    // console.log(node);


                //      const imgs = parent.querySelector('.dz-image img');
                })


                this.on(
            "addedfile", function(file) {
                // alert("not here");
              var caption = file.caption == undefined ? "" : file.caption;
              file._captionLabel = Dropzone.createElement("<div class='flex'><p>File Info:</p>")
              file._captionBox = Dropzone.createElement("<input class='rounded block dark:bg-secondaryDark-black w-full px-4 py-2.25 text-sm shadow-sm placeholder-secondary-500' id='"+file.upload.uuid+"' type='text' name='caption[]'  value='"+caption+"'>");
              // file._preview = Dropzone.createElement("<div class='flex justify-center'> <button type='button' >Preview</button></div>");
              file.previewElement.appendChild(file._captionLabel);
              file.previewElement.appendChild(file._captionBox);
              // file.previewElement.appendChild(file._preview);
              file.previewElement.querySelector('[data-dz-name]').textContent = file.upload.filename;

              file._captionBox.name = file.filename;
              file._captionBox.onkeyup = function() {myMethod()};
                function myMethod() {
                    // alert("hi hi");
                    emit("caption",file._captionBox);
                    // emit("caption-value",file._captionBox.value)
                }


                // file._preview.onclick = function(){
                //   // console.log(element)
                //    var parent = file._preview.parentNode;
                //    console.log(parent);
                //    const imgs = parent.querySelector('.dz-image img');
                //   console.log(imgs)

                // };

                // function img_preview(element) {
                //   alert("here");
                //   var parent = element.parentNode;
                //   console.log(parent);
                //   // const imgs = parent.document.querySelectorAll('.dz-image img');
                //   // console.log(imgs);
                // }

                // const imgs = document.querySelectorAll('.dz-image img');


                // console.log(imgs);


            }),
            this.on(
                "sending", function(file, xhr, formData){
                formData.append('edit_mode',props.edit_mode);
                formData.append('item_id',props.item_id);
                formData.append('max_files',3);
            })

            this.on("removedfile", function(file) {
              emit("removeImage",file.filename);
            } );
            this.on("success", function (file, responseText) {

                // alert(responseText);
                // console.log(responseText)

                if(responseText.msg == "success"){
                    file.filename = responseText.success;
                    // console.log(responseText.success)
                    file._captionBox.name = file.filename;
                    file._captionBox.onkeyup = function() {myMethod()};
                    function myMethod() {
                        // alert("hi hi");
                        emit("caption",file._captionBox);
                        // emit("caption-value",file._captionBox.value)
                    }
                    emit("clicked", file.filename);
                }
                else if(responseText.msg=="fail"){
                    var node, _i, _len, _ref, _results;
                    var message = responseText.success // modify it to your error message
                    // alert(message);
                    file.previewElement.classList.add("dz-error");
                    _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
                    _results = [];
                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                    node = _ref[_i];
                    _results.push(node.textContent = message);
                    }
                    return _results;
                }


            // console.log(this.files);
            // $('form').append('<input type="hidden" name="document[]" value="' + file.name + '">')

            });



        }
        })

        if(props.images){
            props.images.forEach((element) => {
                var url = usePage().props.uploadUrl+'/'+element.img_path;
                let mockFile = { name: "Filename",filename: element.img_path, caption : element.img_desc,upload : {filename:  element.img_path}, size: 12345 };
                myDropzone.displayExistingFile(mockFile, url);

        })}

    }
})

// function img_preview() {
//                   alert("hi its work")
//                 }


</script>

<template>
    <div>
        <div ref="dropRef" class="dropzone custom-dropzone "></div>

    </div>
</template>

<style>
.dropzone .dz-preview.dz-image-preview{
    background: none;
}
.dropzone {
  --tw-text-opacity: 1;
  border: 2px dotted rgb(107 114 128 / var(--tw-text-opacity));
    color: rgb(107 114 128 / var(--tw-text-opacity));
  margin: 2rem 0;
  row-gap: 16px;
}

.dz-image{
   margin-left: auto;
  margin-right: auto;
}
small {
  display: block;
  text-align: center;
}
h1 {
  font-size: 1em;
}


</style>
