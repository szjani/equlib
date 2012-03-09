<?php
namespace Equ\Auth;

/**
 * Authenticates user with Doctrine repository
 *
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        $Link$
 * @since       0.1
 * @version     $Revision$
 * @author      Szurovecz János <szjani@szjani.hu>
 */
class DoctrineAdapter implements \Zend_Auth_Adapter_Interface {

  /**
   * @var Authenticator
   */
  private $repository;

  /**
   *
   * @var string
   */
  private $principal;

  /**
   * @var string
   */
  private $password;

  /**
   * @param Authenticator $repo
   * @param string $principal
   * @param string $password
   */
  public function __construct(Authenticator $repo, $principal, $password) {
    $this->repository = $repo;
    $this->principal  = $principal;
    $this->password   = $password;
  }

  /**
   * @return \Zend_Auth_Result
   */
  public function authenticate() {
    $result = null;
    try {
      $user = $this->repository->authenticate($this->principal, $this->password);
      $result = new \Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
    } catch (\Exception $e) {
      $result = new \Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, null);
    }
    return $result;
  }

}