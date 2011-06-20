<?php
namespace Equ\Form;
use
  PHPUnit_Framework_TestCase,
  Equ\Object\Helper as ObjectHelper,
  Form\Fixture\CommentForm,
  Form\Fixture\Comment,
  Form\Fixture\Author,
  Form\Fixture\AuthorForm;

class MapperTest extends PHPUnit_Framework_TestCase {

  private $em;

  public function setUp() {
    $classLoader = new \Doctrine\Common\ClassLoader('Form\Fixture', __DIR__ . '/../');
    $classLoader->register();

    $config = new \Doctrine\ORM\Configuration();
    $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
    $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
    $config->setProxyDir(__DIR__ . '/Proxy');
    $config->setProxyNamespace('Equ\Form\Proxy');
    $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver());

    $conn = array(
      'driver' => 'pdo_sqlite',
      'memory' => true,
    );

    $evm = new \Doctrine\Common\EventManager();
    $this->em = \Doctrine\ORM\EntityManager::create($conn, $config, $evm);

//    $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
//    $schemaTool->dropSchema(array());
//    $schemaTool->createSchema(array(
//      $this->em->getClassMetadata(self::TEST_ENTITY_CLASS)
//    ));
  }
  
  public function testMapPOPOByClassName() {
    $form = new CommentForm();
    $form->getElement('text')->setValue('commentText');
    $subform = $form->getSubForm('author');
    $subform->getElement('email')->setValue('test@host.com');
    $subform->getElement('name')->setValue('authorName');
    
    $objectHelpers = new \ArrayObject();
    $objectHelpers['comment'] = new ObjectHelper('Form\Fixture\Comment');
    $objectHelpers['author']  = new ObjectHelper('Form\Fixture\Author');
    
    $mapper = new Mapper($form, 'comment', $objectHelpers);
    $mapper->map();
    $comment = $mapper->getObject();
    
    /* @var $comment Form\Fixture\Comment */
    self::assertType('Form\Fixture\Comment', $comment);
    self::assertType('Form\Fixture\Author', $comment->getAuthor());
    self::assertEquals('commentText',   $comment->getText());
    self::assertEquals('test@host.com', $comment->getAuthor()->getEmail());
    self::assertEquals('authorName',    $comment->getAuthor()->getName());
  }
  
  public function testMapPOPOByObjects() {
    $author  = new Author('author-name_orig');
    $comment = new Comment($author);
    $author->setEmail('author@host.com');
    $comment->setText('comment-text');
    
    $form = new CommentForm();
    $form->getElement('text')->setValue('formCommentText');
    $subform = $form->getSubForm('author');
    $subform->getElement('email')->setValue('formtest@host.com');
    $subform->removeElement('name');
    
    $objectHelpers = new \ArrayObject();
    $objectHelpers['comment'] = new ObjectHelper($comment);
    $objectHelpers['author']  = new ObjectHelper($author);
    
    $mapper = new Mapper($form, 'comment', $objectHelpers);
    $mapper->map();
    
    self::assertEquals('formCommentText',   $comment->getText());
    self::assertEquals('formtest@host.com', $comment->getAuthor()->getEmail());
    self::assertEquals('author-name_orig',  $comment->getAuthor()->getName());
  }
  
}
