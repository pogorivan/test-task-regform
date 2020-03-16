<?php
namespace TestTask;

class User {
    private const TABLE_NAME = 'users';

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * string
     */
    public $email;

    /**
     * string
     */
    public $photo;

    /**
     * string
     */
    public $password;

    /**
     * @param \PDO $dbh
     * @param $id
     * @return self|null
     */
    public static function findById(\PDO $dbh, $id)
    {
        $stmt = $dbh->prepare('select * from '.self::TABLE_NAME.' where id = ?');
        if ($stmt->execute([$id])) {
            if ($row = $stmt->fetch()) {
                $user = new self();
                $user->loadFromRow($row);
                return $user;
            }
        }

        return null;
    }

    /**
     * @param \PDO $dbh
     * @param $email
     * @return self|null
     */
    public static function findByEmail(\PDO $dbh, $email) {
        $stmt = $dbh->prepare("select * from ".self::TABLE_NAME." where email = ?");
        if ($stmt->execute([$email])) {
            if ($row = $stmt->fetch()) {
                $user = new self();
                $user->loadFromRow($row);
                return $user;
            }
        }

        return null;
    }

    /**
     * @param \PDO $dbh
     * @param $email
     * @param $password
     * @return self|null
     */
    public static function findByEmailPassword(\PDO $dbh, $email, $password) {
        if ($user = self::findByEmail($dbh, $email)) {
            if (sodium_crypto_pwhash_str_verify($user->password, $password)) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @param \PDO $dbh
     * @return bool
     */
    public function register(\PDO $dbh) {
        $stmt = $dbh->prepare("insert into ".self::TABLE_NAME." (`email`, `password`, `name`) values (?, ?, ?)");
        if ($stmt->execute([$this->email, self::passwordEncode($this->password), $this->name])) {
            $this->id = $dbh->lastInsertId();

            if ($this->photo) {
                $fileName = 'user-photo-'.$this->id.'.'.pathinfo($this->photo["name"])['extension'];
                if (move_uploaded_file($this->photo['tmp_name'], __DIR__.'/../uploads/'.$fileName)) {
                    $stmt = $dbh->prepare("update " . self::TABLE_NAME . " set photo = ? where id = ?");
                    $stmt->execute([$fileName, $this->id]);
                }
            }

            return true;
        }

        return false;
    }

    private function loadFromRow($row) {
        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->email = $row['email'];
        $this->photo = $row['photo'];
        $this->password = $row['password'];
    }

    /**
     * @param $password
     * @return string
     */
    private static function passwordEncode($password) {
        return sodium_crypto_pwhash_str($password, \SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE, \SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE);
    }
}