<div class="modal-dialog" role="document">
    <div class="modal-content">
         <div class="modal-header">
              <h5 class="modal-title" id="myModalLabel1">Cambiar password</h5>
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
                                <label class="form-label">Ingrese su nuevo password</label>
                                <input class="form-control" type="password" name="nuevaPassword" required="">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Repetir password</label>
                                <input class="form-control" type="password" name="nuevaPassword2" required="">
                            </div>
                            <input type="hidden" value="<?php echo $_POST["id"]; ?>" name="idClientePass"/>
                                                                                 
                            <input type="submit" class="btn btn-block btn-info btn-lg" value="Cambiar password">
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