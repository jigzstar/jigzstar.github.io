<?php
  
App::uses('EmagineCommerceAppController', 'EmagineCommerce.Controller');
class ProductsController extends EmagineCommerceAppController {

    public $name = 'Products';		

    // public $helpers = array('Ajax','Html','Javascript','Session','DatePicker');

    

    public $displayField = 'title';

//    public $components = array('RequestHandler','Email','ForceDownload','SwfUploadx');

		//public $components = array('RequestHandler','SwfUpload','Thumb');
		public $components = array('RequestHandler','SwfUpload','Paginator');

    public $paginate = array('limit' => 4, 'page' => 1);
 

    public function beforeFilter() {

      parent::beforeFilter();

			// $this->layout = 'admin';

			
/*
			$i = new Inflector();

			$this->controller = $i->variable($this->name);

			$this->model = $i->singularize($this->name);

			$this->singular_title = $i->humanize($i->underscore($this->model));

			$this->plural_title = $i->pluralize($this->singular_title);

			

			$this->set('controller',$this->controller);

			$this->set('model',$this->model);

			$this->set('singular_title',$this->singular_title);

			$this->set('plural_title',$this->plural_title);

*/    $this->Security->unlockedActions = array('admin_upload_photo');

      $this->Auth->allow(array('index','view','add_to_cart','update_cart','empty_cart','cart','delete'));

    }

    

    public function admin_index() {

      //$this->{$this->model}->Behaviors->attach('Containable');
      /*$this->paginate = array(
      'contain' => array(
        
        'ProductVariationCategory' => array(
          
          'fields' => array('id','title','product_id','description'),
          
          'ProductVariation' => array( 'fields' => array('id','title','product_variation_category_id','price_modifier','description'),
          ),
        ),
        
        'ProductAddon' => array('fields' => array('id','title','product_id','price_modifier','description'),
        ), 
        
        'ProductCategory' => array('fields' => array('id','title'),),
      ),
      // we just need a few things about each post
      'fields' => array('id','name','price','monthly_price','annual_price','fpath','enabled'),
      // only get posts that are published
      //'conditions' => array(),
      );*/
      /*$this->paginate=array();
      $this->paginate['conditions'] = array('Product.id' => 80);*/
      $this->Product->bindModel(array('hasOne' => array('Plugin' =>array(
          'className' => 'PluginManager.Plugin',
          'foreignKey' => 'product_id',))));
      //pr('TEST');
  		$this->set('list', $this->paginate($this->model, array('Package.id' => null), array('name'), array('limit'=>100)));

  	}    

    

    public function admin_add() {

  		if (!empty($this->request->data)) {
          //pr($this->request->data);

  			if ($this->request->data[$this->model]['File']['size']) {

          

          // upload the file

          // use these to configure the upload path, web path, and overwrite settings if necessary

          $this->SwfUpload->uploadpath = 'files'.DS.$this->controller.DS.'videos'.DS;

          $this->SwfUpload->webpath = '/files/'.$this->controller.'/videos/';

          $this->SwfUpload->overwrite = false; //by default, SwfUploadComponent does NOT overwrite files

          

          if ($this->SwfUpload->upload($this->model,'File')) {

              // save the file to the db, or do whatever you want to do with the data

              $this->request->data[$this->model]['video_path'] = $this->SwfUpload->uploadpath.$this->SwfUpload->filename;

          } else {

              $this->Session->setFlash($this->SwfUpload->errorMessage);

          }

        }

	  		if ($this->request->data[$this->model]['ThumbFile']['size']) {

          // upload the file

          // use these to configure the upload path, web path, and overwrite settings if necessary

          $this->SwfUpload->uploadpath = 'files'.DS.$this->controller.DS.'thumbs'.DS;

          $this->SwfUpload->webpath = '/files/'.$this->controller.'/thumbs/';

          $this->SwfUpload->overwrite = false; //by default, SwfUploadComponent does NOT overwrite files

          

          if ($this->SwfUpload->upload($this->model, 'ThumbFile')) {

              // save the file to the db, or do whatever you want to do with the data

            $this->request->data[$this->model]['fpath'] = $this->SwfUpload->uploadpath.$this->SwfUpload->filename;

          } else {

              $this->Session->setFlash($this->SwfUpload->errorMessage);

          }

        }

        

        // Save table        

        if ($this->{$this->model}->saveAll($this->request->data, array('deep'=>true))) {

  	      $this->Session->setFlash('Your ' . $this->singular_title . ' has been saved.');

          $this->redirect(array('admin'=>true,'controller'=>$this->controller,'action'=>'index'));

        }

  		}
		$this->set('productCategories', $this->{$this->model}->ProductCategory->find('list'));

  	}

      	

    public function admin_delete($id) {

    	$this->{$this->model}->removeFile($id,'video_path');

    	$this->{$this->model}->removeFile($id,'fpath');

    	$this->{$this->model}->delete($id, $cascade=true);

    	$this->Session->setFlash('The ' . $this->singular_title . ' with id: '.$id.' has been deleted.');

      $this->redirect(array('admin'=>true,'controller'=>$this->controller,'action'=>'index'));

    }

    

    public function admin_edit($id = null) {

    	$this->{$this->model}->id = $id;

    	if (empty($this->request->data)) {

    		$this->request->data = $this->{$this->model}->find('first',array('conditions'=>array('Product.id'=>$id)));

    	} else {

  			if ($this->request->data[$this->model]['File']['size']) {

  				$this->{$this->model}->removeFile($id,'video_path');

		          // upload the file
		
		          // use these to configure the upload path, web path, and overwrite settings if necessary
		
		        $this->SwfUpload->uploadpath = 'files'.DS.$this->controller.DS.'videos'.DS;
		
		        $this->SwfUpload->webpath = '/files/'.$this->controller.'/videos/';
		
		        $this->SwfUpload->overwrite = false; //by default, SwfUploadComponent does NOT overwrite files
		
		        if ($this->SwfUpload->upload($this->model,'File')) {
		
		              // save the file to the db, or do whatever you want to do with the data
		
		        	$this->request->data[$this->model]['video_path'] = $this->SwfUpload->uploadpath.$this->SwfUpload->filename;
		
		        } else {
		
		              $this->Session->setFlash($this->SwfUpload->errorMessage);
		
		        }
		
		    }
		
	  		if ($this->request->data[$this->model]['ThumbFile']['size']) {
          if(!empty($this->request->data[$this->model]['remove_logo'])){
            $this->{$this->model}->removeFile($id);
          }
  				$this->{$this->model}->removeFile($id,'fpath');

	          	// upload the file
	
	          	// use these to configure the upload path, web path, and overwrite settings if necessary
	
	          	$this->SwfUpload->uploadpath = 'files'.DS.$this->controller.DS.'thumbs'.DS;
	
	          	$this->SwfUpload->webpath = '/files/'.$this->controller.'/thumbs/';
	
	          	$this->SwfUpload->overwrite = false; //by default, SwfUploadComponent does NOT overwrite files
	
	          	if ($this->SwfUpload->upload($this->model, 'ThumbFile')) {
	
	              // save the file to the db, or do whatever you want to do with the data
	
	          		$this->request->data[$this->model]['fpath'] = $this->SwfUpload->uploadpath.$this->SwfUpload->filename;

	        	} else {
	
	              $this->Session->setFlash($this->SwfUpload->errorMessage);
	
	        	}
	
	        }
	        // Save table
                 
	       
	        if ($this->{$this->model}->saveAll($this->request->data, array('deep'=>true))) {
	
	  	      $this->Session->setFlash('Your ' . $this->singular_title . ' has been saved.');
	
	          $this->redirect(array('admin'=>true,'controller'=>$this->controller,'action'=>'index'));
	
	        }

  		}
		
		$this->set('productCategories', $this->{$this->model}->ProductCategory->find('list'));
    //$this->set('webinars', $this->{$this->model}->Webinar->find('list'));
    }

		

	public function index() {

     // $this->layout = 'default';
         //create an fill products table for this example
        /* Load Model datasource */
    /*
                App::import('Model', 'ConnectionManager');
        $con = new ConnectionManager;
        $cn = $con->getDataSource('default');
        // User table schema 
        $sql = "CREATE TABLE IF NOT EXISTS products(
								id INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
								name VARCHAR( 255 ) NOT NULL ,
								price VARCHAR( 20 ) NOT NULL ,
								created DATETIME NOT NULL ,
                                                                modified DATETIME NOT NULL ,
								PRIMARY KEY ( `id` ) ,
								INDEX ( `name` )
								)";
        if ($cn->query($sql)) {
            // Load User Model 
            $this->loadModel('Product');
            // Records array 
            $records_arr = array('Product' => array(
                    array('id' => 1, 'name' => 'pencil', 'price' => '2'),
                    array('id' => 2, 'name' => 'ruler', 'price' => '3'),
                    array('id' => 3, 'name' => 'eraser', 'price' => '2.5'),
                    ));
            foreach ($records_arr as $arr) {
                // Save data to User table  
                if ($this->Product->saveall($arr)) {
                    
                }
            }
            //$this->Session->setFlash(__('Product Table Created Successfully in Database'),'default',array('class'=>''));
        } else {

            $this->Session->setFlash(__('Product Table Already Exist !!!'), 'default', array('class' => ''));
        }
        */
        //find all products
        // $products = $this->Product->find('all')
        $list = $this->paginate($this->model, array('Product.enabled' => 1));
        
        //set counter to display number of products in a cart
        $counter = 0;
        if ($this->Session->read('Counter')) {
            $counter = $this->Session->read('Counter');
        }
        //pass variable to view
        $this->set(compact('list', 'counter'));

      // $this->set('list', $this->paginate($this->model, array('enabled' => 1)));

    }



    public function view($id = null) {

      // $this->layout = 'default';

      //    $this->{$this->model}->id = $id;

      //      $this->request->data = $this->{$this->model}->read();
      
      $this->{$this->model}->Behaviors->attach('Containable');

      $this->{$this->model}->contain(array(
        'ProductVariationCategory' => array('ProductVariation'),
        'ProductAddon',
        'ProductCategory'
      ));
	  
	     $this->{$this->model}->id = $id;
        //check if product exists in database
        if (!$this->{$this->model}->exists()) {
            throw new NotFoundException(__('Invalid product'));
        }
        //read product data
        $this->data = $this->{$this->model}->read(null, $id);
        //set counter to display number of products in a cart
        $counter = 0;
        if ($this->Session->read('Counter')) {
            $counter = $this->Session->read('Counter');
        }
        //pass variable to view
        $this->set(compact('counter'));

    }

/*    

    public function view($slug = null) {

    	$this->layout = 'default';

			if (!$slug) {  

				$this->Session->setFlash('Invalid ID for ' . $this->singular_title . '.');  

				$this->redirect(array('admin'=>true,'controller'=>$this->controller,'action'=>'index'));  

			}      

			$this->request->data = $this->{$this->model}->findBySlug($slug);

			$this->pageTitle = $this->request->data[$this->model]['title'];

    }

*/		 

    public function add_to_cart($id = null) {
      $this->Product->id = $id;
      //check if product exists in database
      if (!$this->Product->exists()) {
          throw new NotFoundException(__('Invalid product'));
      }

      //check if prodocut is in a cart
      $productsInCart = $this->Session->read('Cart');
      if(empty($productsInCart)){
        $productsInCart = array();
      }
      $alreadyIn = false;
      foreach ($productsInCart as $key => $productInCart) {
        if (isset($productInCart['Product']['id'])){
          if(!empty($productInCart['Package']['id'])){
            $this->Session->delete('Cart.'.$key);
            break;
          }
          if ($productInCart['Product']['id'] == $id) {
              $alreadyIn = true;
          }
        }
      }
      $package_id =null;
      $freetrial = false;
      //if product isn't in a cart add it and set counter value
      if (!$alreadyIn ) {
        //pr($this->request->params['pass'][1]);
        if(!empty($this->request->params['pass'][1])){
          $this->Session->write('freetrial',true);
          $freetrial = true;
        }else if ($this->Session->check('freetrial')){
          
            $this->Session->delete('freetrial');
        }
        //pr($this->Session->read('freetrial'));
        //return;
          //if($this->request->params['pass'][3])
          $amount = count($productsInCart);
          //$this->Product->recursive = -1;
          $this->{$this->model}->Behaviors->attach('Containable');

          $this->{$this->model}->contain(array(
            'Package'=>array(),'ProductCategory'=>array('id','title'),
          ));
    			$product = $this->Product->read(null, $id);//pr($product);


          $package_id = $product['Package']['id'];


          /*if(!empty($product['Package']['id'])&& $this->packageExist()){
            $this->Session->setFlash(__('Already have a package in cart'));
            return $this->redirect(array('controller' => 'packages', 'action' => 'cart'));
          }else{
            $product['Package'] = array();
          }*///pr($this->request->params['pass'][4]);
          if(!empty($this->request->params['pass'][3])){
            $product['Website']['id'] = $this->request->params['pass'][3];
          }
          if(!empty($this->request->params['pass'][4])){
            $product['Package']['theme'] = $this->request->params['pass'][4];
          }else{
            $product['Package']['theme']=null;
          }
          $product['Package']['domain'] = null;
          if(!empty($this->request->query['recurring'])){
            $product['Product']['recurring'] = $this->request->query['recurring'];
            $product['Product']['price'] = $this->request->query['price'];
          }else{
            $product['Product']['recurring'] = 2;
            $product['Product']['price'] = $product['Product']['monthly_price'];

          }
    			if(!empty($this->request->query['product_quantity'])){
    				$product['Product']['product_quantity'] = $this->request->query['product_quantity'];
    			}else{
    				$product['Product']['product_quantity'] = 1;
    			}
          if(!empty($this->request->query['ProductAddon'])){
            $this->{$this->model}->ProductAddon->recursive = -1;
            $product['Product']['ProductAddon'] = $this->{$this->model}->ProductAddon->find('all', array('conditions'=>array('ProductAddon.id'=>$this->request->query['ProductAddon'])));
          }else{
            $product['Product']['ProductAddon'] = array();
          }
          $product['Product']['ProductVariationCategory'] = array();
          if(!empty($this->request->query['ProductVariationCategory'])){
            $this->{$this->model}->ProductVariationCategory->recursive = -1;
            foreach($this->request->query['ProductVariationCategory'] AS $pvckey => $pvcat){
              $current_pvcat = $this->{$this->model}->ProductVariationCategory->read(null, $pvckey);
              $this->{$this->model}->ProductVariationCategory->ProductVariation->recursive = -1;
              $current_variation = $this->{$this->model}->ProductVariationCategory->ProductVariation->read(null, $pvcat);
              $current_pvcat['ProductVariation'] = $current_variation['ProductVariation'];
              $product['Product']['ProductVariationCategory'][] = $current_pvcat;
            }

            
          }
          
                // $this->Session->write('Cart.' . $amount, $this->Product->read(null, $id));
          $identifier = date('Y-m-d-H-i-s');
    			$this->Session->write('Cart.' . $identifier, $product);
          $this->Session->write('Counter', $amount + 1);
          $this->Session->setFlash(__('Product added to cart'));
        } else {
          $this->Session->setFlash(__('Product already in cart'));
        }
        if(!$this->themeIsset()){
          return $this->redirect(array('plugin'=>'theme_manager','controller' => 'themes', 'action' => 'themesByPackage',$package_id));
        }
        if($freetrial){
          return $this->redirect(array('plugin'=>'site_builder','controller' => 'websites', 'action' => 'search_subdomain'));
        }
        return $this->redirect(array('plugin'=>'site_builder','controller' => 'websites', 'action' => 'search_domain'));
        $this->redirect(array('controller' => 'packages', 'action' => 'cart'));
       
    }   

    private function packageExist() {
      $productsInCart = $this->Session->read('Cart');
      if(!empty($productsInCart)){
        foreach ($productsInCart as $productInCart) {
              if (!empty($productInCart['Package']['id'])) { 
                return true;
              }
          } 
        }
       // pr($productsInCart);
       return false;
    }

    private function themeIsset() {
      $productsInCart = $this->Session->read('Cart');
      
      foreach ($productsInCart as $productInCart) {
            if (isset($productInCart['Package']['id'])) {
              if ($productInCart['Package']['theme'] == null) { 
                return false;
              }else{
                return true;
              }

            }
        } 
       // pr($productsInCart);
       return false;
    }

    public function delete($id = null) {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        //delete product from cart
        if ($this->Session->delete('Cart.' . $id)) {
            //sort cart elements 
            $cart = $this->Session->read('Cart');
            sort($cart);
            $this->Session->write('Cart', $cart);
            //updeate counter
            $this->Session->write('Counter', count($cart));
            $this->Session->setFlash('Product has been deleted');
        }
        return $this->redirect(array('action' => 'cart'));
    }
	
	public function update_cart($id = null) {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        //update product quantity in cart
        $cart_item = $this->Session->read('Cart.' . $id);
        if (!empty($cart_item)) {
            //sort cart elements 
            
            $cart = $this->Session->read('Cart'); 
			
			if(!empty($this->request->query['product_quantity'])){
				$cart[$id]['Product']['product_quantity'] = $this->request->query['product_quantity'];
			}else{
				$cart[$id]['Product']['product_quantity'] = 1;
			}
			
            //sort($cart);
            $this->Session->write('Cart', $cart);
            //updeate counter
            $this->Session->write('Counter', count($cart));
            $this->Session->setFlash('Product quantity has been updated');
        }
        return $this->redirect(array('action' => 'cart'));
    }

	public function update_website($id = null) {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        //update product quantity in cart
        $cart_item = $this->Session->read('Cart.' . $id);
        if (!empty($cart_item)) {
            //sort cart elements 
            
            $cart = $this->Session->read('Cart'); 
      
      if(!empty($this->request->query['website'])){
        $cart[$id]['Product']['website'] = $this->request->query['website'];
      }else{
        $cart[$id]['Product']['website'] = null;
      }
      
            //sort($cart);
            $this->Session->write('Cart', $cart);
            //updeate counter
            $this->Session->write('Counter', count($cart));
            $this->Session->setFlash('Website has been updated');
        }
        return $this->redirect(array('action' => 'cart'));
    }

	public function cart() {
        //show all elemnts in a cart
        $cart = array();

        if ($this->Session->check('Cart')) {
            $cart = $this->Session->read('Cart');
            if(!$this->themeIsset()){
              $this->redirect(array('plugin'=>'theme_manager','controller' => 'themes', 'action' => 'index'));
            }
        }
        $this->loadModel('SiteBuilder.Website');
        $websites = $this->Website->find('list', array('conditions'=>array('Website.user_id'=>$this->Auth->User('id'))));
        $this->set(compact(array('cart','websites')));
        //$this->set(compact('website'));
    }

    public function empty_cart() {
        //delete cart with all elements and counter

        $this->Session->delete('Cart');
        $this->Session->delete('Counter');
		    $this->Session->setFlash('Your cart is now empty');
        $this->redirect(array('controller' => 'products', 'action' => 'index'));
    }

  public function admin_upload_photo($pic_model_prefix = null, $product_id = null) {
  
//    set_time_limit ( 240 ) ;

    $this->layout = 'ajax';

      $this->render(false);

    //Configure::write('debug', 2);
    $pic_model = $pic_model_prefix.'Pic';
    
    $this->request->data[$pic_model]['product_id'] = $product_id;
    
    $this->Product->{$pic_model}->create();
    
   // pr($this->request->data);
    
    $this->Product->ProductPic->save($this->request->data);
    
    echo json_encode($this->Product->{$pic_model}->findById($this->Product->{$pic_model}->id));
    //echo json_encode($this->request->data);
    

  }
  
  public function admin_delete_photo($id = null, $pic_model_prefix = null) {
  
    $this->layout = 'ajax';

    $this->autoRender = false;

    if (!$id) {

      echo json_encode(array('status' => 0, 'msg' => __d('gallery','Invalid photo. Please try again.', true))); exit();

    }
    
    $pic_model = $pic_model_prefix.'Pic';

    if ($this->Product->{$pic_model}->delete($id)) {

      echo json_encode(array('status' => 1)); exit();

    } else {

      echo json_encode(array('status' => 0,  'msg' => __d('gallery','Problem removing ' . $pic_model . ' photo. Please try again.', true))); exit();

    }

  }

}

?>