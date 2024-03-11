@extends('admin.admin_master')

@section('admin')

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                    
                    <h4 class="card-title">Edit Category</h4>
                    <hr>
                    <form class="forms-sample" action="{{ url('category/update/'.$category->id)  }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="old_image" value="{{ $category->category_image }}">
                        <div class="form-group">
                            <label for="exampleInputName1">Catecory Name</label>
                            <input type="text" class="form-control" name="category_name" id="category_name" placeholder="Category Name" value="{{ $category->category_name }}">

                            @error('category_name')
                                <span class="text-danger"> {{ $message }} </span>
                            @enderror
                        
                        </div>
                        
                        
                        
                        <div class="form-group">
                            <label for="exampleInputEmail1">Update Category Image</label>
                            <input type="file" name="category_image" class="form-control" id="category_image"  value="{{ $category->category_image }}">

                                @error('category_image')
                                    <span class="text-danger"> {{ $message }}</span>
                                @enderror

                        </div>


                        <div class="form-group">
                            <img src="{{ asset($category->category_image) }}" style="width:400px; height:200px;" >
                        </div>


                        <button type="submit" class="btn btn-gradient-primary me-2">Update</button>
                        
                        <a href="{{ route('all.category') }}">
                            <button type="button" class="btn btn-light">Cancel</button>
                        </a>
                    </form>


            </div>
        </div>
    </div>
   
  
 @endsection

 <img src="{{ asset($category->category_image) }}" style="height:40px; width:70px;" > 