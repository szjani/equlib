<?php
namespace Equ\Form;
use
  PHPUnit_Framework_TestCase,
  Equ\Object\Helper as ObjectHelper,
  Form\Fixture\CommentForm,
  Form\Fixture\Comment,
  Form\Fixture\Author,
  Form\Fixture\AuthorForm,
  Form\Fixture\CommentType;

class BuilderTest extends PHPUnit_Framework_TestCase {

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
  
  public function testBuildByClassName() {
    $builder = new Builder('Form\Fixture\Comment', new \Equ\Form\ElementCreator\Dojo\Factory());
    $builder->setEntityManager($this->em);
    $formType = new CommentType();
    $formType->buildForm($builder);
    $commentForm = $builder->getForm();
    
    self::assertEquals('comment', $commentForm->getElementsBelongTo());
    self::assertNotNull($commentForm->getElement('text'));
    self::assertEquals('', $commentForm->getElement('text')->getValue());
    self::assertNotNull($commentForm->getSubForm('author'));
    $authorForm = $commentForm->getSubForm('author');
    self::assertNotNull($authorForm->getElement('name'));
    self::assertNotNull($authorForm->getElement('email'));
    self::assertEquals('', $authorForm->getElement('email')->getValue());
    self::assertEquals('', $authorForm->getElement('name')->getValue());
  }
  
  public function testMapPOPOByObjects() {
    $author  = new Author('author-name_orig');
    $comment = new Comment($author);
    $author->setEmail('author@host.com');
    $comment->setText('comment-text');
    
    $builder = new Builder($comment, new \Equ\Form\ElementCreator\Dojo\Factory());
    $builder->setEntityManager($this->em);
    $formType = new CommentType();
    $formType->buildForm($builder);
    $commentForm = $builder->getForm();
    
    self::assertEquals('comment', $commentForm->getElementsBelongTo());
    self::assertNotNull($commentForm->getElement('text'));
    self::assertEquals('comment-text', $commentForm->getElement('text')->getValue());
    self::assertNotNull($commentForm->getSubForm('author'));
    $authorForm = $commentForm->getSubForm('author');
    self::assertNotNull($authorForm->getElement('name'));
    self::assertNotNull($authorForm->getElement('email'));
    self::assertEquals('author@host.com', $authorForm->getElement('email')->getValue());
    self::assertEquals('author-name_orig', $authorForm->getElement('name')->getValue());
  }
  
}
