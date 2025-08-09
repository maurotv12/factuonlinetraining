<div class="modal-dialog" role="document">
    <div class="modal-content">
         <div class="modal-header">
              <h5 class="modal-title" id="myModalLabel1">Cambiar foto de perfil</h5>
              <button type="button" class="close rounded-pill"
                  data-bs-dismiss="modal" aria-label="Close">
                  <i data-feather="x"></i>
              </button>
          </div>
            <div class="modal-body">

            <div class="card sobraCrearLink">
                <div class="card-body"> 
                     <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="formFile" class="form-label">Subir foto</label>
                                <input class="form-control" type="file" id="formFile" name="nuevaImagen" required="">
                            </div>
                            <input type="hidden" value="<?php echo $_POST["id2"]; ?>" name="idClienteImagen"/>
                            <input type="hidden" name="pagina" value="<?php echo $_POST["id"]; ?>">
                           
                           
                            <input type="submit" class="btn btn-block btn-info btn-lg" value="Cambiar Foto">
                      </form>
                     
                </div>
            </div>

            </div>
           <div class="modal-footer">
            <button type="button" class="btn btn-outline-info ml-1" data-bs-dismiss="modal">
                <i class="bx bx-x d-block d-sm-none"></i>
                <span class="d-none d-sm-block">Cerrar</span>
            </button>                                
          </div>  
                                        
    </div>
                                   
</div>  