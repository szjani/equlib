<?php
namespace Equ\Form;
use
  PHPUnit_Framework_TestCase,
  Equ\Object\Helper as ObjectHelper,
  Form\Fixture\CommentForm,
  Form\Fixture\Comment,
  Form\Fixture\Author,
  Form\Fixture\AuthorForm,
  Form\Fixture\ArticleForm;

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
    $objectHelpers['comment']         = new ObjectHelper('Form\Fixture\Comment');
    $objectHelpers['comment-author']  = new ObjectHelper('Form\Fixture\Author');
    
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
    $objectHelpers['comment']         = new ObjectHelper($comment);
    $objectHelpers['comment-author']  = new ObjectHelper($author);
    
    $mapper = new Mapper($form, 'comment', $objectHelpers);
    $mapper->map();
    
    self::assertEquals('formCommentText',   $comment->getText());
    self::assertEquals('formtest@host.com', $comment->getAuthor()->getEmail());
    self::assertEquals('author-name_orig',  $comment->getAuthor()->getName());
  }
  
  public function testMapPOPOCollection() {
    $form = new ArticleForm();
    $form->getElement('text')->setValue('articleText');
    $subform = $form->getSubForm('author');
    $subform->getElement('email')->setValue('test@host.com');
    $subform->getElement('name')->setValue('authorName');
    $comment0 = $form->getSubForm('comments[0]');
    $comment0Author = $comment0->getSubForm('author');
    $comment0Author->getElement('name')->setValue('c1AuthorName');
    $comment0Author->getElement('email')->setValue('c1AuthorEmail');
    $comment1 = $form->getSubForm('comments[1]');
    $comment1->getElement('text')->setValue('comment2Text');
    $comment1Author = $comment1->getSubForm('author');
    $comment1Author->getElement('name')->setValue('c2AuthorName');
    $comment1Author->getElement('email')->setValue('c2AuthorEmail');
    
    $objectHelpers = new \ArrayObject();
    $objectHelpers['article']             = new ObjectHelper('Form\Fixture\Article');
    $objectHelpers['article-author']      = new ObjectHelper('Form\Fixture\Author');
    $objectHelpers['article-comments[0]'] = new ObjectHelper('Form\Fixture\Comment');
    $objectHelpers['article-comments[1]'] = new ObjectHelper('Form\Fixture\Comment');
    $objectHelpers['article-comments[0]-author']  = new ObjectHelper('Form\Fixture\Author');
    $objectHelpers['article-comments[1]-author']  = new ObjectHelper('Form\Fixture\Author');
    
    $mapper = new Mapper($form, 'article', $objectHelpers);
    $mapper->map();
    $article = $mapper->getObject();
    
    self::assertEquals('articleText', $article->getText());
    self::assertType('\ArrayObject',  $article->getComments());
    $comments = $article->getComments();
    self::assertEquals(2, $comments->count());
    self::assertEquals('comment2Text', $comments[1]->getText());
    self::assertEquals('c1AuthorName', $comments[0]->getAuthor()->getName());
    self::assertEquals('c2AuthorName', $comments[1]->getAuthor()->getName());
    self::assertEquals('c1AuthorEmail', $comments[0]->getAuthor()->getEmail());
    self::assertEquals('c2AuthorEmail', $comments[1]->getAuthor()->getEmail());
  }
  
}
