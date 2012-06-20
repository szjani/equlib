<?php
namespace Equ\Form;
use
  PHPUnit_Framework_TestCase,
  Equ\Object\Helper as ObjectHelper,
  Form\Fixture\CommentForm,
  Form\Fixture\Comment,
  Form\Fixture\Author,
  Form\Fixture\AuthorForm,
  Form\Fixture\CommentType,
  Form\Fixture\ArticleType,
  Form\Fixture\Article;

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
    $builder = new Builder('Form\Fixture\Comment', $this->em, new \Equ\Form\ElementCreator\Dojo\Factory());
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
    
    $builder = new Builder($comment, $this->em, new \Equ\Form\ElementCreator\Dojo\Factory());
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
  
  public function testMapPOPOCollectionByObjects() {
    $author1  = new Author('author1-name_orig');
    $comment1 = new Comment($author1);
    $author1->setEmail('author1@host.com');
    $comment1->setText('comment1-text');
    $author2  = new Author('author2-name_orig');
    $comment2 = new Comment($author2);
    $author2->setEmail('author2@host.com');
    $comment2->setText('comment2-text');
    
    $articleAuthor = new Author('article-author');
    $articleAuthor->setEmail('article-author@host.com');
    
    $article = new Article('article-text', $articleAuthor);
    $article->setComments(new \ArrayObject(array($comment1, $comment2)));
    
    $builder = new Builder($article, $this->em, new \Equ\Form\ElementCreator\Dojo\Factory());
    $formType = new ArticleType();
    $formType->buildForm($builder);
    $articleForm = $builder->getForm();
    
    self::assertEquals('article', $articleForm->getElementsBelongTo());
    self::assertNotNull($articleForm->getElement('text'));
    self::assertEquals('article-text', $articleForm->getElement('text')->getValue());
    
    self::assertNotNull($articleForm->getSubForm('author'));
    $articleAuthorForm = $articleForm->getSubForm('author');
    self::assertEquals('article-author', $articleAuthorForm->getElement('name')->getValue());
    self::assertEquals('article-author@host.com', $articleAuthorForm->getElement('email')->getValue());
    
    self::assertNotNull($articleForm->getSubForm('comments[0]'));
    self::assertNotNull($articleForm->getSubForm('comments[1]'));
    
    $commentForm0 = $articleForm->getSubForm('comments[0]');
    self::assertNotNull($commentForm0->getElement('text'));
    self::assertEquals('comment1-text', $commentForm0->getElement('text')->getValue());
    
    $commentForm1 = $articleForm->getSubForm('comments[1]');
    self::assertNotNull($commentForm1->getElement('text'));
    self::assertEquals('comment2-text', $commentForm1->getElement('text')->getValue());
    
    self::assertNotNull($commentForm0->getSubForm('author'));
    $authorCommentForm0 = $commentForm0->getSubForm('author');
    
    self::assertNotNull($commentForm1->getSubForm('author'));
    $authorCommentForm1 = $commentForm1->getSubForm('author');
    
    self::assertNotNull($authorCommentForm0->getElement('email'));
    self::assertNotNull($authorCommentForm0->getElement('name'));
    self::assertNotNull($authorCommentForm1->getElement('email'));
    self::assertNotNull($authorCommentForm1->getElement('name'));
    self::assertEquals('author1@host.com', $authorCommentForm0->getElement('email')->getValue());
    self::assertEquals('author1-name_orig', $authorCommentForm0->getElement('name')->getValue());
    self::assertEquals('author2@host.com', $authorCommentForm1->getElement('email')->getValue());
    self::assertEquals('author2-name_orig', $authorCommentForm1->getElement('name')->getValue());
    
//    var_dump($builder->getObjectHelpers());
  }
  
}
