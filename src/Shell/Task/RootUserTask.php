<?php

namespace Backend\Shell\Task;

use Cake\Console\Shell;

/**
 * @property \Backend\Model\Table\UsersTable $Users
 */
class RootUserTask extends Shell
{
    /**
     * {@inheritDoc}
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->description(__d('backend', "Create backend root user"))
            ->addOption('email', [
                'help' => 'Root user email',
                'short' => 'e'
            ])
            ->addOption('password', [
                'help' => 'Root user password',
                'short' => 'p'
            ]);

        return $parser;
    }

    /**
     * @return void
     */
    public function main()
    {
        $this->out("-- Setup root user --");
        foreach ($this->args as $key => $val) {
            $this->out("Arg: $key - $val");
        }

        $this->loadModel('Backend.Users');
        $rootCount = $this->Users->find()->where(['Users.username' => 'root'])->count();
        if ($rootCount > 0) {
            $this->error('Root user already exists');
        }

        do {
            $email = trim($this->in("Enter root email address: "));
            $strlen = strlen($email);
        } while ($strlen < 1);

        do {
            $pass1 = trim($this->in("Choose root password: "));
            if (strlen($pass1) < 1) {
                $this->out("Please enter a password");
                continue;
            }

            $pass2 = trim($this->in("Repeat password: "));

            $match = ($pass1 === $pass2);
            if (!$match) {
                $this->out("Passwords do not match. Please try again.");
            }
        } while (!$match);

        $root = $this->Users->createRootUser($email, $pass1);
        if ($root === false) {
            $this->error("Failed to create root user");
        }

        $this->out("<success>Root user successfully created!</success>");
    }
}
