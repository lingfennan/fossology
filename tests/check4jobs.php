#!/usr/bin/php
<?php


/***********************************************************
 Copyright (C) 2008 Hewlett-Packard Development Company, L.P.

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 version 2 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along
 with this program; if not, write to the Free Software Foundation, Inc.,
 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 ***********************************************************/

/**
 * Are there any jobs running?
 *
 * NOTE: this program depends on the UI testing infrastructure at this
 * point.
 * @return boolean (TRUE or FALSE)
 *
 * @version "$Id: $"
 *
 * @TODO: add a parameter to allow for how long to wait?
 *
 * Created on Jan. 15, 2009
 */

require_once ('TestEnvironment.php');
require_once ('testClasses/db.php');

define('SQL', "SELECT *
          FROM jobqueue
          INNER JOIN job ON jobqueue.jq_job_fk = job.job_pk
          LEFT OUTER JOIN upload ON upload_pk = job.job_upload_fk
          LEFT JOIN jobdepends ON jobqueue.jq_pk = jobdepends.jdep_jq_fk
          WHERE (jobqueue.jq_starttime IS NULL OR jobqueue.jq_endtime IS
          NULL OR jobqueue.jq_end_bits > 1)
          ORDER BY upload_filename,upload.upload_pk,job.job_pk,jobqueue.jq_pk," .
"jobdepends.jdep_jq_fk;");

class check4jobs {

  private $jobCount;
  private $Db;

  function __construct() {
    $myDB = new db();
    $connection = $myDB->connect();
    if (!(is_resource($connection))) {
      print "{$argv[0]}:FATAL ERROR!, could not connect to the data-base\n";
      return(NULL);
    }
    $this->dbConn = $connection;
    return;
  }

  public function Check() {
    $this->_ck4j();
    return($this->jobCount);
  }
  private function _ck4j() {

    $results = $this->Db->dbQuery(SQL);
    if (empty ($results)) {
      $this->jobCount = 0;
    } else {
      $howMany = count($results);
      $this->jobCount = $howMany;
    }
  }

  public function getJobCount() {
    return($this->jobCount);
  }
} // check4jobs
?>
