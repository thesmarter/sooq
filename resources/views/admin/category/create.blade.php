@extends('admin.admin_master')

@section('admin')

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                    
                    <h4 class="card-title">Create Category</h4>
                    <hr>
                    <form class="forms-sample" action="{{ route('add.category') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="catName">Catecory Name</label>
                            <input type="text" class="form-control" name="category_name" id="category_name" placeholder="Category Name">

                            @error('category_name')
                                <span class="text-danger"> {{ $message }} </span>
                            @enderror
                        
                        </div>

                        <div class="form-group">
                            <label for="catImage">Category Image</label>
                            <input type="file" name="category_image" class="form-control" id="category_image" >

                                @error('category_image')
                                    <span class="text-danger"> {{ $message }}</span>
                                @enderror

                        </div>


                        
                        <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                        
                        <a href="{{ route('all.category') }}">
                            <button type="button" class="btn btn-light">Cancel</button>
                        </a>
                    </form>


            </div>
        </div>
    </div>
   
  
 @endsection
