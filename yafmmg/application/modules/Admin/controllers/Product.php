<?php
class ProductController extends AdminController {

	public function indexAction(){

	}

	//产品列表
	public function productListAction(){
		$mod = new ProductModel();
		$category = $mod->categoryList("");
		$new_category = array();
		foreach($category as $key=> $category_v){
			$v = array("id"=>$category_v['id'],'pId'=>$category_v['parentid'],"name"=>$category_v['name']);
			$new_category[] = $v;
		}
		$category_json = json_encode($new_category);
		$this->assign("category_json",$category_json); //左侧分类
		$params = array();
		$params['page'] = $this->get("page");
		$params['pagesize'] = $this->get("pagesize");
		$params['is_search'] = $this->get("is_search");
		$params['cat_id'] = $this->get("cat_id");
		//$params['is_search'] =1;
		$params['keyword'] = $this->get("keyword");
		$params['ctime'] = $this->get("ctime");
		//$params['ctime'] ='2017-4-15';
		$where_arr = array();
		if($params['is_search']){
			if($params['keyword']){
				$where_arr['keyword'] = $params['keyword'];
			}
			if($params['ctime']){
				$where_arr['ctime'] = strtotime($params['ctime']);
			}
		}
		if($params['cat_id']){
			$where_arr['cat_id'] = $params['cat_id'];
		}
		$page = !empty($params["page"]) ? $params["page"] : 1;
		$pageSize = !empty($params["pagesize"]) ? $params["pagesize"] : 2000;
		$start = ($page - 1) * $pageSize;
		$count = ProductModel::getProductList($start, $pageSize, $where_arr, true);
		$productList = ProductModel::getProductList($start, $pageSize, $where_arr);
		$this->assign("count",$count); //条数
		$this->assign("productList",$productList); //左侧分类
	}
	//添加商品
	public function productAddAction(){
		$mod = new ProductModel();
		$category = $mod->categoryList("");

		$all_all_attribute = $mod->getAllAttribute(0, 100,array("status"=>1,"type"=>1));
		$this->assign("all_all_attribute",$all_all_attribute); //品牌选择列表
		$new_category = array();
		foreach($category as $key=> $category_v){
			$v = array("id"=>$category_v['id'],'pId'=>$category_v['parentid'],"name"=>$category_v['name']);
			$new_category[] = $v;
		}

		$category_json = json_encode($new_category);
		$this->assign("category_json",$category_json); //左侧分类
		$product_serve = ProductModel::getAllProductServe();
		$this->assign("product_serve",$product_serve);  //商品服务列表
		$categorySelectList = ProductModel::categorySelectList("");
		$this->assign("categorySelectList",$categorySelectList); //分类选择列表
		$brandlist = ProductModel::brandList();
		$this->assign("brandlist",$brandlist); //品牌选择列表
	}
	//商品编辑
	public function productUpdataPageAction(){
		$pid = $this->get("id");
		$mod = new ProductModel();
		$category = $mod->categoryList("");
		$new_category = array();
		foreach($category as $key=> $category_v){
			$v = array("id"=>$category_v['id'],'pId'=>$category_v['parentid'],"name"=>$category_v['name']);
			$new_category[] = $v;
		}
		$all_all_attribute = $mod->getAllAttribute(0, 100,array("status"=>1,"type"=>1));
		$this->assign("all_all_attribute",$all_all_attribute); //品牌选择列表
		$product_attr = ProductModel::getProductAttribute($pid);
		$productAttribute = array();
		foreach($product_attr as $attribute_key=>$attribute_value){
			$attribute_value_new = array();
			$productAttribute[$attribute_value['attr_id']]['attr_id'] = $attribute_value['attr_id'];
			$productAttribute[$attribute_value['attr_id']]['attr_name'] = $attribute_value['attr_name'];
			$productAttribute[$attribute_value['attr_id']]['attr_value_list'][$attribute_value['id']] = $attribute_value['attr_value_name'];
			if($attribute_value['is_default']==1){
				$productAttribute[$attribute_value['attr_id']]['default'] =  $attribute_value['attr_value_id'];
			}
		}
		$this->assign("productAttribute",$productAttribute); //产品属性
		$product_data = $mod->getProductById($pid);
		if($product_data){
			$product_data['product_serve'] = explode(",",$product_data['product_serve']);
			$this->assign("product_data",$product_data);
			$category_json = json_encode($new_category);
			$this->assign("category_json",$category_json); //左侧分类
			$product_serve = ProductModel::getAllProductServe();
			$this->assign("product_serve",$product_serve);  //商品服务列表
			$categorySelectList = ProductModel::categorySelectList($product_data['catid']);
			$this->assign("categorySelectList",$categorySelectList);  //分类选择列表
			$brandlist = ProductModel::brandList();
			$this->assign("brandlist",$brandlist); //品牌选择列表
		}else{
			echo "产品不存在！";
			exit;
		}

	}

	//单个删除产品   批量删除(不建议使用)
	public function delProductAction(){
		$pid = $this->getPost("id");
		$result = ProductModel::delProduct($pid);
		if ($result) {
			$code = 1000;
			$data = array();
		} else {
			$code = 1004;
			$data = array();
		}
		$this->apiReturn($data, $code);
	}

	//单个删除产品   批量删除(软删除)
	public function softDeleltProductAction(){
		$pid = $this->getPost("id");
		$result = ProductModel::softDeleltProduct($pid);
		if ($result) {
			echo json_encode(array("status"=>"1000","result"=>$result));
		} else {
			echo json_encode(array("status"=>"1004","result"=>$result));
		}
		exit;
	}
	//产品修改，添加
	public function updataproductAction(){
		$post = $this->getPost();                
		$attr_arr = array();                
		if(isset($post['attribute']) && $post['attribute']){                          
			foreach($post['attribute'] as $attribute_key=>$attribute_v){                            
				if($attribute_v!=0){
					$attr = explode(":",$attribute_v);
					$attr_id = $attr[0];
					$attr_name = $attr[1];                                        
					if(isset($post['attribute_value'][$attribute_key]) && $post['attribute_value'][$attribute_key]){                                           
						$post['attribute_value'][$attribute_key] = str_replace("，",",",$post['attribute_value'][$attribute_key]);                                                 
                                                 $p_v_arr = array();
						if(isset($post['product_id']) && $post['product_id']){

							$p_v_list = ProductModel::getProductAttributeByAttrId($post['product_id'],$attr_id,"attr_value_name");
							
							foreach($p_v_list as $k=>$v){
								$p_v_arr[] = $v['attr_value_name'];
							}
						}
                                                
						if(isset($post['attribute_value'][$attribute_key])){
							$attribute_value = explode(",",$post['attribute_value'][$attribute_key]);
							$p_v_arr = array_flip(array_flip($p_v_arr));
							$result=array_diff_assoc($attribute_value,$p_v_arr);
							if(empty($result)){
								$result=array_diff_assoc($p_v_arr,$attribute_value);
							}
							$attr_arr[$attr_id]['attr_name'] = $attr_name;
							$attr_arr[$attr_id]['attr_v_list'] = $attribute_value;
							if(empty($result)){
								unset($attr_arr[$attr_id]);
							}
						}
					}else{
						$attr_arr[$attr_id] = array();
					}
				}else{
					$attr_arr[0] = array();
				}
			}
		}
		unset($post['attribute']);
		unset($post['attribute_value']);
		$content = $this->getPost("content",false); //内容部分不过滤
		if($content){
			$post['content'] = $content;
		}
		$info = $this->upload("","custom",array(),"product_img");
		if(is_array($info)){
			//上传背景图片
			if($info){
				if (isset($_FILES['product_img']) && $_FILES['product_img']['name']){
					$post['product_img'] = $info['product_img']['savename'];
				}
			}else{
				$this->error($info);
			}
		}
		if(isset($post['product_serve']) && $post['product_serve']){
			if(is_array($post['product_serve'])){
				$post['product_serve'] = implode(",",$post['product_serve']);
			}
                }else{
                    $post['product_serve']="";
                }

		$product_id = ProductModel::updataProduct($post,$attr_arr);
		if(isset($post['is_ajax']) && $post['is_ajax']){
			if($product_id){
				echo json_encode(array("status"=>"1000","product_id"=>$product_id));
			}else{
				echo json_encode(array("status"=>"1004","product_id"=>""));
			}
			exit;
		}else{
			$url = "/index.php/admin/product/productList";
			jsRedirect($url);
			exit;
		}
	}
	//品牌管理
	public function brandManageAction(){
		$params = array();
		$params['page'] = $this->get("page");
		$params['pagesize'] = $this->get("pagesize");
		$params['is_search'] = $this->get("is_search");

		$params['keyword'] = $this->get("keyword");
		$params['ctime'] = $this->get("ctime");

		$where_arr = array();
		if($params['is_search']){
			if($params['keyword']){
				$where_arr['keyword'] = $params['keyword'];
			}
			if($params['ctime']){
				$where_arr['ctime'] = strtotime($params['ctime']);
			}

		}
		$page = !empty($params["page"]) ? $params["page"] : 1;
		$pageSize = !empty($params["pagesize"]) ? $params["pagesize"] : 100;
		$start = ($page - 1) * $pageSize;
		$count = ProductModel::getBrandList($start, $pageSize, $where_arr, true);
		$brandlist = ProductModel::getBrandList($start, $pageSize, $where_arr);
		$where_arr['is_domestic']=1;
		$nei_count = ProductModel::getBrandList($start, $pageSize, $where_arr, true);
		$where_arr['is_domestic']=2;
		$wai_count = ProductModel::getBrandList($start, $pageSize, $where_arr, true);

		$this->assign("nei_count",$nei_count); //国内条数
		$this->assign("wai_count",$wai_count); //国外条数
		$this->assign("count",$count); //条数
		$this->assign("brandlist",$brandlist); //左侧分类
	}
	//添加品牌
	public function addBrandAction(){
		$brand_post = $this->getPost();
		if(isset($brand_post['dosubmit'])){
			$info = $this->upload("","custom",array(),"brand_img");
			if(is_array($info)){
				//上传背景图片
				if($info){
					if (isset($_FILES['img']) && $_FILES['img']['name']){
						$brand_post['img'] = $info['img']['savename'];
					}
				}else{
					$this->error($info);
				}
			}
			$brand_id = ProductModel::updataBrand($brand_post);
			$url = "/index.php/admin/product/brandManage";
			jsRedirect($url);
		}
	}
   //品牌编辑
   public function updataBrandAction(){
	   $brand_post = $this->getPost();
	   $brand_id = $this->get("brand_id");
	   if(isset($brand_post['dosubmit'])){
		   $info = $this->upload("","custom",array(),"brand_img");
		   if(is_array($info)){
			   //上传背景图片
			   if($info){
				   if (isset($_FILES['img']) && $_FILES['img']['name']){
					   $brand_post['img'] = $info['img']['savename'];
				   }
			   }else{
				   $this->error($info);
			   }
		   }
		   $re = ProductModel::updataBrand($brand_post);
		   if(isset($brand_post['is_ajax']) && $brand_post['is_ajax']){
			   if($re){
				   echo json_encode(array("status"=>"1000","brand_id"=>$brand_id));
			   }else{
				   echo json_encode(array("status"=>"1004","brand_id"=>""));
			   }
			   exit;
		   }else{
			   if($re){
				   $url = "/index.php/admin/product/updataBrand/brand_id/".$brand_id;

			   }else{
				   $url = "/index.php/admin/product/brandManage";
			   }
			   jsRedirect($url);
		   }
	   }else{

		   $brand = ProductModel::getBrandById($brand_id);
		   $this->assign("brand",$brand); //分类页详情
	   }
   }
	//单个删除品牌   批量删除(软删除)
	public function softDeleltBrandAction(){
		$pid = $this->getPost("id");
		$result = ProductModel::softDeleltBrand($pid);
		if ($result) {
			echo json_encode(array("status"=>"1000","result"=>$result));
		} else {
			echo json_encode(array("status"=>"1004","result"=>$result));
		}
		exit;
	}

	//分类管理
	public function categoryManageAction(){
		$mod = new ProductModel();
		$category = $mod->categoryList("");
		$new_category = array();
		foreach($category as $key=> $category_v){
			$v = array("id"=>$category_v['id'],'pId'=>$category_v['parentid'],"name"=>$category_v['name']);
			$new_category[] = $v;
		}
		$category_json = json_encode($new_category);
		$this->assign("category_json",$category_json); //左侧分类
	}

	//添加分类
	public function productCategoryAddAction(){
		$cat_post = $this->getPost();
		$parent_id = $this->get("parentid");
		if($parent_id){
			$this->assign("parentid",$parent_id); //左侧分类
		}else{
			$this->assign("parentid",0); //左侧分类
		}
		$mod = new ProductModel();
		$category = $mod->categoryList("");
		$new_category = array();
		foreach($category as $key=> $category_v){
			$v = array("id"=>$category_v['id'],'pId'=>$category_v['parentid'],"name"=>$category_v['name']);
			$new_category[] = $v;
		}
		$category_json = json_encode($new_category);
		$this->assign("category_json",$category_json); //左侧分类
		if(isset($cat_post['dosubmit'])){
			$info = $this->upload("","custom",array(),"category_img");
			if(is_array($info)){
				//上传背景图片
				if($info){
					if (isset($_FILES['img']) && $_FILES['img']['name']){
						$cat_post['img'] = $info['img']['savename'];
					}
				}else{
					$this->error($info);
				}
			}
			$category_id = ProductModel::updataCategory($cat_post);
			if($category_id){
				$url = "/index.php/admin/product/productCategoryUpdata/cat_id/".$category_id;

			}else{
				$url = "/index.php/admin/product/productCategoryAdd";
			}
			jsRedirect($url);
		}
	}
	//分类编辑页面
	public function productCategoryUpdataAction(){
		$cat_post = $this->getPost();
		$cat_id = $this->get("cat_id");
		if(isset($cat_post['dosubmit'])){
			$info = $this->upload("","custom",array(),"category_img");
			if(is_array($info)){
				//上传背景图片
				if($info){
					if (isset($_FILES['img']) && $_FILES['img']['name']){
						$cat_post['img'] = $info['img']['savename'];
					}
				}else{
					$this->error($info);
				}
			}
			ProductModel::updataCategory($cat_post);
			$url = "/index.php/admin/product/productCategoryUpdata/cat_id/".$cat_post['cat_id'];
			jsRedirect($url);
		}else{
			$category = ProductModel::getCategoryById($cat_id);
			$this->assign("category",$category); //分类页详情

		}

	}

	//单个删除类目   批量删除(软删除)
	public function softDeleltCategoryAction(){
		$cat_id = $this->getPost("id");
		$result = ProductModel::softDeleltCategory($cat_id);
		if ($result) {
			echo json_encode(array("status"=>"1000","result"=>$result));
		} else {
			echo json_encode(array("status"=>"1004","result"=>$result));
		}
		exit;
	}


	//swfupload上传图片
	public function swfupload_ajaxAction()
	{
		$subdir = '/';
		//$subdir .= date('Y/m/d/', time());
		$upload = new Tool\UploadFile();
		$upload->maxSize  = 3145728;// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath =  UPLOAD_PATH;// 设置附件上传目录
		$upload->autoSub = true;//开启子目录
		$upload->subType = 'custom';// 子目录创建方式 可以使用hash date custom
		$upload->subDir = $subdir;
		$upload->thumb = true;//开启缩略图
		$upload->thumbType = 0;
		$upload->thumbMaxWidth = '750';
		$upload->thumbMaxHeight = '750';
		$upload->thumbPrefix = '750_750_';
		$upload_path = str_replace(APP_PATH,"",UPLOAD_PATH);
		if(!$upload->upload()) {// 上传错误提示错误信息
			echo $upload->getErrorMsg();
		}else{// 上传成功 获取上传文件信息
			$files = $upload->getUploadFileInfo();
			$q = new \Qiniu\QiNiuOperate();
			foreach ($files as $file){
				$img = $file['savename'];
				$thumb = '750_750_'.basename($img);
				$rs1 = $q->upload(UPLOAD_PATH.$subdir.$thumb, $thumb);
				if($rs1){
					@unlink(UPLOAD_PATH.$subdir.$thumb);
				}

				$rs2 = $q->upload(UPLOAD_PATH.$subdir.basename($img), basename($img));
				if($rs2){
					@unlink(UPLOAD_PATH.$subdir.basename($img));
				}
				echo IMG_URL.$thumb.'|'.$thumb.'|'.IMG_URL.basename($img);exit;
			}
		}

	}
	//获取产品服务列表
	public function productServeListAction(){
		$params = array();
		$params['page'] = $this->get("page");
		$params['pageSize'] = $this->get("pageSize");
		$page = !empty($params["page"]) ? $params["page"] : 1;
		$pageSize = !empty($params["pageSize"]) ? $params["pageSize"] : 10;
		$where = array("1" => 1);

		$start = ($page - 1) * $pageSize;
		$count = ProductModel::productServeList($start, $pageSize, $where, true);
		$productservelist = ProductModel::productServeList($start, $pageSize,  $where);
		$this->assign("count",$count);
		$this->assign("productservelist",$productservelist);
	}

	//添加产品服务
	public function addProductServeAction(){
		$product_serve_post = $this->getPost();
		if(isset($product_serve_post['dosubmit'])){
			$serve_id = ProductModel::addProductServe($product_serve_post);
			$url = "/index.php/admin/product/brandManage";
			jsRedirect($url);
		}
	}

	//删除产品服务
	public function delProductServeAction()
	{
		$id = $this->getPost("id");
		$result = ProductModel::delProductServe($id);
		if($result){
			echo json_encode(array("status"=>"1000","id"=>$result));
		}else{
			echo json_encode(array("status"=>"1004","id"=>0));
		}
		exit;
	}

	public function updateProductServeAction(){
		$product_serve_id = $this->get("product_serve_id");
		$data = $this->getPost();
		if(isset($data['dosubmit']) && $data['dosubmit']){
			$result = ProductModel::updataProductServe($data['id'],$data);
			if(isset($data['is_ajax']) && $data['is_ajax']){
				if($result){
					echo json_encode(array("status"=>"1000","id"=>$result));
				}else{
					echo json_encode(array("status"=>"1004","id"=>""));
				}
				exit;
			}else{
				$url = "/index.php/admin/product/productServeList";
				jsRedirect($url);
			}
		}else{
			$product_serve = ProductModel::getProductServeById($product_serve_id);
			$this->assign("product_serve",$product_serve);
		}

	}
	//获取产品服务列表
	public function attributeListAction(){
		$params = array();
		$params['page'] = $this->get("page");
		$params['pageSize'] = $this->get("pageSize");
		$page = !empty($params["page"]) ? $params["page"] : 1;
		$pageSize = !empty($params["pageSize"]) ? $params["pageSize"] : 10;
		$where = array("1" => 1);

		$start = ($page - 1) * $pageSize;
		$count = ProductModel::attributeList($start, $pageSize, $where, true);
		$attributeList = ProductModel::attributeList($start, $pageSize,  $where);
		$this->assign("count",$count);
		$this->assign("attributeList",$attributeList);
	}

	//添加产品服务
	public function addAttributeAction(){
		$product_serve_post = $this->getPost();
		if(isset($product_serve_post['dosubmit'])){
			ProductModel::addAttribute($product_serve_post);
			$url = "/index.php/admin/product/attributeList";
			jsRedirect($url);
		}
	}

	//删除产品服务
	public function delAttributeAction()
	{
		$id = $this->getPost("id");
		$result = ProductModel::delAttribute($id);
		if($result){
			echo json_encode(array("status"=>"1000","id"=>$result));
		}else{
			echo json_encode(array("status"=>"1004","id"=>0));
		}
		exit;
	}
	//更新产品服务
	public function updateAttributeAction(){
		$id = $this->get("id");
		$data = $this->getPost();
		if(isset($data['dosubmit']) && $data['dosubmit']){
			$result = ProductModel::updateAttribute($data['id'],$data);
			if(isset($data['is_ajax']) && $data['is_ajax']){
				if($result){
					echo json_encode(array("status"=>"1000","id"=>$result));
				}else{
					echo json_encode(array("status"=>"1004","id"=>""));
				}
				exit;
			}else{
				$url = "/index.php/admin/product/attributeList";
				jsRedirect($url);
			}
		}else{
			$attribute = ProductModel::getAttributeById($id);
			$this->assign("attribute",$attribute);
		}

	}
}