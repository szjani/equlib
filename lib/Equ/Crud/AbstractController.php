<?php
namespace Equ\Crud;
use
  Doctrine\ORM\EntityManager,
  Equ\Entity\FormBuilder,
  Equ\Controller\Request\FilterDTOBuilder,
  Equ\Message,
  Equ\Crud\Exception\InvalidArgumentException,
  Equ\Crud\Exception\UnexpectedValueException,
  Equ\Crud\Exception\RuntimeException,
  Equ\Doctrine\IPaginatorCreator,
  Equ\Form\IBuilder;

/**
 * Controller of CRUD operations
 *
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        $Link$
 * @since       2.0
 * @version     $Revision$
 * @author      Szurovecz János <szjani@szjani.hu>
 */
abstract class AbstractController extends \Zend_Controller_Action {

  /**
   * @var array
   */
  protected $ignoredFields = array();

  /**
   * @var boolean
   */
  protected $useFilterForm = true;

  /**
   * Adds general CRUD script path
   */
  public function init() {
    parent::init();
    $this->view->addScriptPath(dirname(__FILE__) . '/views/scripts');
    $title = $this->view->pageTitle =
    	"Navigation/{$this->_getParam('module')}/{$this->_getParam('controller')}/{$this->_getParam('action')}/label";
    $this->view->headTitle($this->view->translate($title));
  }
  
  /**
   * @return \Equ\Form\IMappedType
   */
  public abstract function getMainForm();
  
  /**
   * @return \Equ\Form\IMappedType
   */
  public abstract function getFilterForm();
  
  /**
   * @param IBuilder $builder 
   */
  protected function formBuilderCreated(IBuilder $builder) {}
  
  /**
   * @param object $entity 
   */
  protected function prePersist($entity) {}
  
  /**
   * @param object $entity 
   */
  protected function postPersist($entity) {}
  
  /**
   * @param object $entity
   */
  protected function preFlush($entity) {}
  
  /**
   * @param object $entity
   */
  protected function postFlush($entity) {}
  
  /**
   * @param object $entity
   */
  protected function preRemove($entity) {}
  
  /**
   * @param object $entity
   */
  protected function postRemove($entity) {}
  
  /**
   * @return \Equ\Form\IMappedType 
   */
  public function getUpdateForm() {
    return $this->getMainForm();
  }
  
  /**
   * @return \Equ\Form\IMappedType 
   */
  public function getCreateForm() {
    return $this->getMainForm();
  }
  
  /**
   * @return array of column names
   */
  public function getTableFieldNames() {
    $metadata = $this->getEntityManager()->getClassMetadata($this->getEntityClass());
    $fields = $metadata->fieldNames;
    foreach ($metadata->associationMappings as $fieldName => $def) {
      if ($def['isOwningSide']) {
        $fields[] = $fieldName;
      }
    }
    return $fields;
  }

  /**
   * @return string
   */
  protected function getEntityClass() {
    return $this->getMainForm()->getObjectClass();
  }

  /**
   * @return array
   */
  public function getIgnoredFields() {
    return $this->ignoredFields;
  }

  /**
   * @return EntityManager
   */
  protected final function getEntityManager() {
    return $this->_helper->serviceContainer('doctrine.entitymanager');
  }

  /**
   * Enables visibility of hidden navigation items and set id parameter.
   * Useful for update action to show update nav. item.
   *
   * @param int $id
   */
  protected function initHiddenNavigationItemWithId($id) {
    $navigation = $this->_helper->serviceContainer('navigation');
    $page = $navigation->findById(
      "{$this->_getParam('module')}_{$this->_getParam('controller')}_{$this->_getParam('action')}"
    );
    if ($page) {
      $page
        ->setParams(array('id' => $id))
        ->setVisible(true);
    }
  }

  /**
   * If .phtml is in application folder it renders that,
   * otherwise generic .phtml will be rendered
   *
   * @param string $script
   * @param string $name
   */
  public function renderScript($script, $name = null) {
    try {
      $this->render(substr($script, 0, -6), $name);
    } catch (\Zend_View_Exception $e) {
      parent::renderScript($script, $name);
    }
  }
  
  /**
   * Redirects to listAction
   */
  public function indexAction() {
    $this->_helper->redirector->gotoRouteAndExit(array('action' => 'list'));
  }
  
  /**
   * Calls create method of service with form values
   */
  public function createAction() {
    $form = null;
    try {
      $builder = $this->_helper->createFormBuilder($this->getCreateForm(), $this->getEntityClass());
      $this->formBuilderCreated($builder);
      $form = $builder->getForm();
      $em   = $this->getEntityManager();
      if ($this->_request->isPost()) {
        if (!$builder->getMapper()->isValid($this->_request)) {
          throw new RuntimeException('Invalid values in form');
        }
        $entity = $builder->getMapper()->getObject();
        $this->prePersist($entity);
        $em->persist($entity);
        $this->postPersist($entity);
        $this->preFlush($entity);
        $em->flush();
        $this->postFlush($entity);
        $this->_helper->flashMessenger('Crud/Create/Success');
        $this->_helper->redirector->gotoRouteAndExit(array('action' => 'list'));
      }
    } catch (\Exception $e) {
      $this->_helper->serviceContainer('log')->err($e);
      $this->_helper->flashMessenger('Crud/Create/UnSuccess', Message::ERROR);
    }
    $this->view->createForm = $form;
    $this->renderScript('create.phtml');
  }

  /**
   * Calls update method of sevice with form values
   */
  public function updateAction() {
    $id      = $this->_getParam('id');
    $this->initHiddenNavigationItemWithId($id);
    $form = null;
    try {
      $em      = $this->getEntityManager();
      $builder = $this->_helper->createFormBuilder($this->getUpdateForm(), $em->find($this->getEntityClass(), $id));
      $this->formBuilderCreated($builder);
      $form    = $builder->getForm();
      if ($this->_request->isPost()) {
        if (!$builder->getMapper()->isValid($this->_request)) {
          throw new RuntimeException('Invalid values in form');
        }
        $this->preFlush($entity);
        $em->flush();
        $this->postFlush($entity);
        $this->_helper->flashMessenger('Crud/Update/Success');
        $this->_helper->redirector->gotoRouteAndExit(array('action' => 'list'));
      }
    } catch (\Exception $e) {
      $this->_helper->serviceContainer('log')->err($e);
      $this->_helper->flashMessenger('Crud/Update/UnSuccess', Message::ERROR);
    }
    $this->view->updateForm = $form;
    $this->renderScript('update.phtml');
  }

  /**
   * Calls delete method of service
   */
  public function deleteAction() {
    $id = $this->_getParam('id');
    try {
      $em = $this->getEntityManager();
      $entity = $em->find($this->getEntityClass(), $id);
      if (!$entity) {
        throw new Exception\InvalidArgumentException("Invalid id: '$id'");
      }
      $this->preRemove($entity);
      $em->remove($entity);
      $this->postRemove($entity);
      $this->preFlush($entity);
      $em->flush();
      $this->postFlush($entity);
      $this->_helper->flashMessenger('Crud/Delete/Success');
      $this->_helper->redirector->gotoRouteAndExit(array('action' => 'list'));
    } catch (\Exception $e) {
      $this->_helper->serviceContainer('log')->err($e);
      $this->_helper->flashMessenger('Crud/Delete/UnSuccess', Message::ERROR);
    }
    $this->renderScript('delete.phtml');
  }

  /**
   * Calls list method of service,
   * lists items with Zend_Paginator
   */
  public function listAction() {
    $filters = $this->getEntityClass();
    try {
      $this->view->keys        = \array_diff($this->getTableFieldNames(), $this->getIgnoredFields());
      $this->view->currentSort = $this->_getParam('sort');
      $this->view->nextOrder   = $this->_getParam('order', 'ASC') == 'ASC' ? 'DESC' : 'ASC';
      $filterForm = null;
      
      // create filter form
      if ($this->useFilterForm) {
        $builder = $this->_helper->createFormBuilder($this->getFilterForm(), $this->getEntityClass());
        /* @var $filterForm \Zend_Form */
        $filterForm = $builder->getForm();
        $filterForm->setMethod(\Zend_Form::METHOD_GET);
        $this->formBuilderCreated($builder);
        $namespace = $filterForm->getElementsBelongTo();
        if (is_array($this->_request->getParam($namespace)) && !$builder->getMapper()->isValid($this->_request)) {
          throw new RuntimeException('Invalid values in form');
        }
        $filters = $builder->getMapper()->getObject();
      }
      
    } catch (\Exception $e) {
      $this->_helper->serviceContainer('log')->err($e);
      $this->_helper->flashMessenger('Crud/Filter/UnSuccess', Message::ERROR);
    }
    
    if ($this->useFilterForm) {
      $this->view->filterForm  = $filterForm;
    }
    
    // create paginator
    /* @var $paginatorCreator IPaginatorCreator */
    $paginatorCreator = $this->_helper->serviceContainer('paginatorCreator');
    $this->view->paginator = $paginatorCreator->createPaginator(
      $filters,
      $this->_getParam('page', 1),
      $this->_getParam('items', 10),
      $this->_getParam('sort'),
      $this->_getParam('order', 'ASC')
    );
    $this->renderScript('list.phtml');
  }

}