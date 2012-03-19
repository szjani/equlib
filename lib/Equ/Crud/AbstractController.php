<?php
namespace Equ\Crud;
use
  Doctrine\ORM\EntityManager,
  Doctrine\Common\EventManager,
  Doctrine\Common\EventSubscriber,
  Doctrine\ORM\Event\LifecycleEventArgs,
  Doctrine\ORM\Event\PreUpdateEventArgs,
  Equ\Entity\FormBuilder,
  Equ\Controller\Request\FilterDTOBuilder,
  Equ\Message,
  Equ\Crud\Exception\InvalidArgumentException,
  Equ\Crud\Exception\UnexpectedValueException,
  Equ\Crud\Exception\RuntimeException,
  Equ\Doctrine\IPaginatorCreator,
  Equ\Form\IBuilder,
  Equ\Form\IMappedType,
  Equ\Form\Exception\ValidationException,
  Equ\Object\Helper as ObjectHelper;

/**
 * Controller of CRUD operations
 *
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        $Link$
 * @since       2.0
 * @version     $Revision$
 * @author      Szurovecz János <szjani@szjani.hu>
 */
abstract class AbstractController extends \Zend_Controller_Action implements EventSubscriber {

  /**
   * @var EntityManager
   */
  public $em;
  
  /**
   * @var \Zend_Navigation
   */
  public $navigation;
  
  /**
   * @var \Zend_Log
   */
  public $log;
  
  /**
   * @var \Doctrine\Common\EventManager
   */
  public $doctrineEventmanager;
  
  /**
   * @var array
   */
  protected $ignoredFields = array();

  /**
   * @var boolean
   */
  protected $useFilterForm = true;
  
  /**
   * @var string
   */
  protected $entityClass = null;

  public function getSubscribedEvents() {
    return array(
      'prePersist',
      'postPersist',
      'preRemove',
      'postRemove',
      'preUpdate',
      'postUpdate',
    );
  }
  
  /**
   * Adds general CRUD script path
   */
  public function init() {
    parent::init();
    $this->view->addScriptPath(dirname(__FILE__) . '/views/scripts');
    
    $contextSwitch = $this->_helper->getHelper('contextSwitch');
    $contextSwitch
      ->addActionContext('delete', 'json')
      ->addActionContext('update', 'json')
      ->addActionContext('list', 'json')
      ->addActionContext('create', 'json')
      ->addActionContext('lookup', 'json')
      ->initContext();
    
    $this->doctrineEventmanager->addEventSubscriber($this);
  }
  
  public function postDispatch() {
    if (!$this->_helper->viewRenderer->getNoRender()) {
      $this->renderScript($this->_request->getParam('action') . '.phtml');
    }
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
   * @param LifecycleEventArgs $args
   */
  public function prePersist(LifecycleEventArgs $args) {}
  
  /**
   * @param LifecycleEventArgs $args
   */
  public function postPersist(LifecycleEventArgs $args) {}
  
  /**
   * @param PreUpdateEventArgs $args 
   */
  public function preUpdate(PreUpdateEventArgs $args) {}
  
  /**
   * @param LifecycleEventArgs $args
   */
  public function postUpdate(LifecycleEventArgs $args) {}
  
  /**
   * @param LifecycleEventArgs $args
   */
  public function preRemove(LifecycleEventArgs $args) {}
  
  /**
   * @param LifecycleEventArgs $args
   */
  public function postRemove(LifecycleEventArgs $args) {}
  
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
   *
   * @param IMappedType $mappedType
   * @param mixed $object
   * @return FormBuilder 
   */
  protected function createFormBuilder(IMappedType $mappedType, $object) {
    return $this->_helper->createFormBuilder($mappedType, $object);
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
    if (null === $this->entityClass) {
      $this->entityClass = $this->getMainForm()->getObjectClass();
    }
    return $this->entityClass;
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
    return $this->em;
  }

  /**
   * Enables visibility of hidden navigation items and set id parameter.
   * Useful for update action to show update nav. item.
   *
   * @param int $id
   */
  protected function initHiddenNavigationItemWithId($id) {
    $page = $this->navigation->findById(
      "{$this->_getParam('module')}_{$this->_getParam('controller')}_{$this->_getParam('action')}"
    );
    if ($page) {
      $page
        ->setParams(array('id' => $id))
        ->setVisible(true);
    }
  }
  
  /**
   * @param \Zend_Form $form 
   */
  protected function formHasErrors(\Zend_Form $form) {
    $this->view->formErrors = $form->getMessages();
  }
  
  protected function exceptionIsThrowed(\Exception $e) {
    $this->log->err($e);
    $this->_helper->redirectHereAfterPost->setAutoRedirect(false);
    $this->view->exceptionMessage = $e->getMessage();
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
      $builder = $this->createFormBuilder($this->getCreateForm(), $this->getEntityClass());
      $this->formBuilderCreated($builder);
      $form = $builder->getForm();
      $em   = $this->getEntityManager();
      if ($this->_request->isPost()) {
        if (!$builder->getMapper()->isValid($this->_request)) {
          throw new ValidationException('Invalid values in form');
        }
        $entity = $builder->getMapper()->getObject();
        $em->persist($entity);
        $em->flush();
        $this->_helper->flashMessenger('Crud/Create/Success');
        if (!$this->_request->isXmlHttpRequest()) {
          $this->_helper->redirector->gotoRouteAndExit(array('action' => 'list'));
        }
      }
    } catch (ValidationException $e) {
      if ($form instanceof \Zend_Form) {
        $this->formHasErrors($form);
      }
      $this->_helper->flashMessenger('Crud/Create/UnSuccess', Message::ERROR);
    } catch (\Exception $e) {
      $this->exceptionIsThrowed($e);
      $this->_helper->flashMessenger('Crud/Create/UnSuccess', Message::ERROR);
    }
    $this->view->createForm = $form;
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
      $entity  = $em->find($this->getEntityClass(), $id);
      $builder = $this->createFormBuilder($this->getUpdateForm(), $entity);
      $this->formBuilderCreated($builder);
      $form    = $builder->getForm();
      if ($this->_request->isPost()) {
        if (!$builder->getMapper()->isValid($this->_request)) {
          throw new ValidationException('Invalid values in form');
        }
        $em->flush();
        $this->_helper->flashMessenger('Crud/Update/Success');
        if (!$this->_request->isXmlHttpRequest()) {
          $this->_helper->redirector->gotoRouteAndExit(array('action' => 'list'));
        }
      }
    } catch (ValidationException $e) {
      if ($form instanceof \Zend_Form) {
        $this->formHasErrors($form);
      }
      $this->_helper->flashMessenger('Crud/Update/UnSuccess', Message::ERROR);
    } catch (\Exception $e) {
      $this->exceptionIsThrowed($e);
      $this->_helper->flashMessenger('Crud/Update/UnSuccess', Message::ERROR);
    }
    $this->view->updateForm = $form;
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
      $em->remove($entity);
      $em->flush();
      $this->_helper->flashMessenger('Crud/Delete/Success');
      if (!$this->_request->isXmlHttpRequest()) {
        $this->_helper->redirector->gotoRouteAndExit(array('action' => 'list'));
      }
    } catch (\Exception $e) {
      $this->exceptionIsThrowed($e);
      $this->_helper->flashMessenger('Crud/Delete/UnSuccess', Message::ERROR);
    }
  }
  
  protected function getListQueryBuilder() {
    return null;
  }
  
  /**
   * Usefull for autocomplete fields
   * Use $id or $q but not at the same time
   * 
   * @param string $key Key field in response
   * @param string $value Value field in response
   * @param string $id Search one field by id (init form element)
   * @param string $q  Search value
   */
  public function lookupAction() {
    $this->_helper->contextSwitch->setAutoJsonSerialization(false);
    $repo = $this->em->getRepository($this->getEntityClass());
    echo \Zend_Json::encode($this->_helper->lookUp($repo));
  }

  /**
   * Calls list method of service,
   * lists items with Zend_Paginator
   */
  public function listAction() {
    $filters = array();
    $objectHelper = new ObjectHelper($this->getEntityClass());
    $this->view->keys        = \array_diff($this->getTableFieldNames(), $this->getIgnoredFields());
    $this->view->currentSort = $this->_getParam('sort');
    $this->view->nextOrder   = $this->_getParam('order', 'ASC') == 'ASC' ? 'DESC' : 'ASC';
    
    // create filter form
    if ($this->useFilterForm) {
      $filterForm = null;
      try {
        $builder = $this->createFormBuilder($this->getFilterForm(), $this->getEntityClass());
        /* @var $filterForm \Zend_Form */
        $filterForm = $builder->getForm();
        $this->view->filterForm = $filterForm;
        $filterForm->setMethod(\Zend_Form::METHOD_GET);
        $this->formBuilderCreated($builder);
        $namespace = $filterForm->getElementsBelongTo();
        if (is_array($this->_request->getParam($namespace))) {
          if (!$builder->getMapper()->isValid($this->_request, false)) {
            throw new ValidationException('Invalid values in form');
          }
          $builder->getMapper()->map();
        }
        $filters = $this->_request->getParam($namespace, array());
        $objectHelper = $builder->getObjectHelper();
      } catch (ValidationException $e) {
        if ($filterForm instanceof \Zend_Form) {
          $this->formHasErrors($filterForm);
        }
        $this->_helper->flashMessenger('Crud/Filter/UnSuccess', Message::ERROR);
      } catch (\Exception $e) {
        $this->exceptionIsThrowed($e);
        $this->_helper->flashMessenger('Crud/Filter/UnSuccess', Message::ERROR);
      }
    }
    
    // create paginator
    $jsonRequest = ($this->_getParam('format') == 'json');
    /* @var $paginatorCreator IPaginatorCreator */
    $paginatorCreator = $this->_helper->serviceContainer('paginator.creator');
    $this->view->paginator = $paginatorCreator->createPaginator(
      $objectHelper,
      $filters,
      $this->_getParam('page', 1),
      $this->_getParam('items', 10),
      $this->_getParam('sort'),
      $this->_getParam('order', 'ASC'),
      $this->getListQueryBuilder(),
      $jsonRequest
    );
    $this->view->paginatorParameters = $this->view->paginator->getPages();
    if ($jsonRequest) {
      $this->view->paginator = $this->view->paginator->getCurrentItems()->getArrayCopy();
    }
  }

}