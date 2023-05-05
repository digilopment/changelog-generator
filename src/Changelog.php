<?php

class Changelog
{

    private $repo_url;
    private $log_command = "git log --pretty=format:'%h %ad %s' --date=short --date-order";
    private $log_output;
    private $log_lines;
    private $changes = [];
    private $current_commit = 'HEAD';
    private $dates = [];
    private $changelog;
    private $generatePath;

    public function __construct($repoPath = '', $generatePath = '')
    {
        $this->generatePath = $generatePath;
        $this->setRepoPath($repoPath);
        $this->repo_url = $this->getRepositoryUrl();
        $this->log_output = shell_exec($this->log_command);
        $this->log_lines = explode("\n", $this->log_output);
        $this->parseLogLines();
    }

    private function setRepoPath($path)
    {
        if ($path) {
            if (substr($path, -1) != '/') {
                $path .= '/';
            }
            chdir($path);
        }
    }

    private function setGeneratePath()
    {
        $path = $this->generatePath;
        if ($path) {
            if (substr($path, -1) != '/') {
                $path .= '/';
            }
            return $path;
        }
        return __DIR__ . '/../';
    }

    private function getRepositoryUrl()
    {
        $pattern = '/^(https?:\/\/)?([^@]+@)?([^\/:]+)[\/:]([^\/]+)\/([^\/]+)$/i';
        $url = exec("git config --get remote.origin.url");

        if (preg_match($pattern, $url, $matches)) {
            $urlWithoutGit = 'https://' . $matches[3] . '/' . $matches[4] . '/' . $matches[5];
            return explode('.git', $urlWithoutGit)[0];
        }
        return '';
    }

    private function parseLogLines()
    {
        foreach ($this->log_lines as $line) {
            $parts = explode(' ', $line, 3);
            $hash = $parts[0];
            $this->dates[$hash] = $parts[1];
            $message = $parts[2];

            if (empty($this->changes)) {
                $this->changes[$hash] = [];
            } else {
                $this->changes[$this->current_commit][] = $message;
            }

            $this->current_commit = $hash;
        }
    }

    public function generate()
    {
        $changelog = '';
        $changelog .= "## Change Log\n";
        $changelog .= "### [Unreleased][unreleased]\n\n";
        foreach ($this->changes as $commit => $messages) {
            $changelog .= "### [$commit] - {$this->dates[$commit]}\n";
            $changelog .= "#### Added\n";
            foreach ($messages as $message) {
                $changelog .= "- $message\n";
            }
            $changelog .= "\n";
        }
        $changelog .= "[unreleased]: {$this->repo_url}/compare/{$this->current_commit}...HEAD\n";
        foreach ($this->changes as $commit => $messages) {
            $changelog .= "[$commit]: {$this->repo_url}/commit/$commit\n";
        }

        $this->changelog = $changelog;
        return $this;
    }

    public function writeToFIle()
    {
        file_put_contents($this->setGeneratePath() . 'CHANGELOG.md', $this->changelog);
        return $this;
    }

    public function render()
    {
        print($this->changelog);
    }

}
