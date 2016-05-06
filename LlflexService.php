<?php 
/** 
 * This sample service contains functions that illustrate typical
 * service operations. This code is for prototyping only. 
 *  
 *  Authenticate users before allowing them to call these methods. 
 */ 

class llflexService { 
  var $username = "gdhslitlondon"; 
  var $password = "kuelahth"; 
  var $server = "mysql-user.stanford.edu"; 
  var $port = "3306"; 
  var $databasename = "g_dhs_litlondon"; 
  var $tablename = "entity"; 
  
  var $connection; 
  public function __construct() { 
    $this->connection = mysqli_connect( 
                       $this->server,  
                       $this->username,  
                       $this->password, 
                       $this->databasename, 
                       $this->port 
                       ); 
    
    $this->throwExceptionOnError($this->connection); 
  } 

  public function getEntities() {
     $stmt = mysqli_prepare($this->connection,
          "
          	SELECT
			entity.unique_id,
			entity.name_vern,
			entity.type,
			entity.source_id,
			type_entity.category

			FROM

			entity

			LEFT JOIN type_entity
			ON type_entity.unique_id = entity.type
			
			WHERE
			entity.type = 236
           ");     
         
      $this->throwExceptionOnError();

      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();

      $rows = array();
      mysqli_stmt_bind_result($stmt, $row->unique_id, $row->name_vern,
                    $row->type, $row->source_id, $row->category);

      while (mysqli_stmt_fetch($stmt)) {
          $rows[] = $row;
          $row = new stdClass();
          mysqli_stmt_bind_result($stmt, $row->unique_id, $row->name_vern,
                    $row->type, $row->source_id, $row->category);
      }

      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

      return $rows;
  }
  
    public function getLocations() {
     $stmt = mysqli_prepare($this->connection,
          "
          	SELECT
			entity.unique_id,
			entity.notes as name_vern,
			entity.type,
			entity.source_id,
			type_entity.category

			FROM

			entity

			LEFT JOIN type_entity
			ON type_entity.unique_id = entity.type
			
			WHERE
			entity.type = 20
			
			ORDER BY
			entity.notes
			
           ");     
         
      $this->throwExceptionOnError();

      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();

      $rows = array();
      mysqli_stmt_bind_result($stmt, $row->unique_id, $row->name_vern,
                    $row->type, $row->source_id, $row->category);

      while (mysqli_stmt_fetch($stmt)) {
          $rows[] = $row;
          $row = new stdClass();
          mysqli_stmt_bind_result($stmt, $row->unique_id, $row->name_vern,
                    $row->type, $row->source_id, $row->category);
      }

      $q = 0;
      while ($q < count($rows)) {
      
      $damnname = $rows[$q]->name_vern;
      $newname = utf8_encode($damnname);
      $rows[$q]->name_vern = $newname;
      $q++;
      
      }
      
      
      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

      return $rows;
  }  

  
  
  public function getAttributes() {
     $stmt = mysqli_prepare($this->connection,
          "
          
          	SELECT

			attribute.unique_id,
			attribute.entity_id,
			entity.name_vern AS ent_name,
			attribute.target_entity,
			t_entity.name_vern AS tar_name,
			attribute.type,
			type_attribute.name AS att_type,
			attribute.data_char,
			attribute.data_numeric,
			attribute.begin_date,
			attribute.begin_date_variance,
			attribute.end_date,
			attribute.end_date_variance,
			attribute.instance_date,
			attribute.instance_date_variance,
			attribute.source_id

			FROM

			attribute

			LEFT JOIN entity
			ON entity.unique_id = attribute.entity_id

			LEFT JOIN entity AS t_entity
			ON t_entity.unique_id = attribute.target_entity

			LEFT JOIN  type_attribute
			ON type_attribute.unique_id = attribute.type
          
           
           ");
      $this->throwExceptionOnError();

      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();

      $rows = array();
      mysqli_stmt_bind_result($stmt,
                    $row->id,
                    $row->entity,
                    $row->ent_name,
                    $row->tar_ent,
                    $row->tar_name,
                    $row->type,
                    $row->att_type,
                    $row->data_char,
		    $row->data_numeric, $row->begin_date, $row->begin_date_variance, $row->end_date,
                    $row->end_date_variance, $row->instance_date, $row->instance_date_variance,
                    $row->source_id);

      while (mysqli_stmt_fetch($stmt)) {
          $rows[] = $row;
          $row = new stdClass();
          mysqli_stmt_bind_result($stmt,
                    $row->id, $row->entity, $row->ent_name, $row->tar_ent,
		    $row->tar_name, $row->type, $row->att_type, $row->data_char,
		    $row->data_numeric, $row->begin_date, $row->begin_date_variance, $row->end_date,
                    $row->end_date_variance, $row->instance_date, $row->instance_date_variance,
                    $row->source_id);
      }

      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

      return $rows;
  }  


  public function getEntitiesByID($itemID) {
     $stmt = mysqli_prepare($this->connection,
          "
          
          SELECT DISTINCT

		entity.unique_id,
		entity.name_vern,
		entity.type,
		COALESCE(attlat.data_numeric, 51.508056) AS llat,
		COALESCE(attlong.data_numeric, -0.124722) AS llong

		FROM

		entity

		LEFT JOIN attribute as attlat
		ON attlat.entity_id = entity.unique_id AND attlat.type = 41

		LEFT JOIN attribute as attlong
		ON attlong.entity_id = entity.unique_id AND attlong.type = 42

		WHERE

		entity.unique_id = ?  
          "
     		);
      $this->throwExceptionOnError();
          
      mysqli_stmt_bind_param($stmt, 'i', $itemID);
      $this->throwExceptionOnError();

      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();

      $rows = array();
      mysqli_stmt_bind_result($stmt, $row->unique_id, $row->name_vern,
                    $row->type, $row->llat, $row->llong);

      if (mysqli_stmt_fetch($stmt)) {
                  return $row;
      } else {
                  return null;
          }

      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

  }  

    public function getRelatedEntities($itemID) {
     $stmt = mysqli_prepare($this->connection,
          "
          
          SELECT DISTINCT

entity.unique_id,
entity.name_vern

FROM

entity,
attribute

WHERE

entity.type = 236

AND

(
(attribute.target_entity = entity.unique_id

AND

attribute.entity_id = ?

)

OR

(attribute.entity_id = entity.unique_id

AND

attribute.target_entity = ?
)
)
            
          "
     
     		);
      $this->throwExceptionOnError();
          
      mysqli_stmt_bind_param($stmt, 'ii', $itemID, $itemID);
      $this->throwExceptionOnError();

      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();

      
      
  
      $rows = array();
      mysqli_stmt_bind_result($stmt, $row->unique_id, $row->name_vern);

      while (mysqli_stmt_fetch($stmt)) {
          $rows[] = $row;
          $row = new stdClass();
          mysqli_stmt_bind_result($stmt, $row->unique_id, $row->name_vern);
      }
      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

      return $rows;

  }

  
  public function getEntitiesByType($searchStr) {
     $stmt = mysqli_prepare($this->connection,
          "
          
            SELECT
			entity.unique_id,
			entity.name_vern,
			entity.type,
			entity.source_id,
			type_entity.category

			FROM

			entity

			LEFT JOIN type_entity
			ON type_entity.unique_id = entity.type
          
			WHERE
			entity.type = ?
           
           ");
      $this->throwExceptionOnError();
          
      mysqli_stmt_bind_param($stmt, 'isiis', $searchStr);
      $this->throwExceptionOnError();

      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();

      $rows = array();
      mysqli_stmt_bind_result($stmt, $row->unique_id, $row->name_vern,
                    $row->type, $row->source_id, $row->category);

      while (mysqli_stmt_fetch($stmt)) {
          $rows[] = $row;
          $row = new stdClass();
          mysqli_stmt_bind_result($stmt, $row->unique_id, $row->name_vern,
                    $row->type, $row->source_id, $row->category);
      }

      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

      return $rows;

  }
  

   public function createAssoci($tar, $ent, $fnamer) {
   
   $type = 45;
   if ($fnamer == "none") {
   	$type = 13;
   }
	$stmt = mysqli_prepare($this->connection,
		"
		
INSERT

INTO

attribute
(attribute.entity_id, attribute.target_entity, attribute.type, attribute.data_char)

VALUES
('.$tar.','.$ent.','.$type.','$fnamer')");

	$this->throwExceptionOnError();

	mysqli_stmt_execute($stmt);
	$this->throwExceptionOnError();
	
	$autoid = mysqli_stmt_insert_id($stmt);
	
	mysqli_stmt_free_result($stmt);
	mysqli_close($this->connection);
	
	return $autoid;
  }

   public function createEntity($item) {	$stmt = mysqli_prepare($this->connection,		"INSERT INTO entity (
			unique_id,name_vern,type,source_id) 
		VALUES (?, ?, ?, ?)");	$this->throwExceptionOnError();
	
	mysqli_bind_param($stmt, 'isii', $item->unique_id, $item->name_vern,		$item->type, $item->source_id
	);	$this->throwExceptionOnError();

	mysqli_stmt_execute($stmt);	$this->throwExceptionOnError();
	
	$autoid = mysqli_stmt_insert_id($stmt);
	
	mysqli_stmt_free_result($stmt);	mysqli_close($this->connection);	
	return $autoid;  }

  public function deleteEntity($itemID) {	$stmt = mysqli_prepare($this->connection,		"DELETE FROM entity WHERE id = ?");	$this->throwExceptionOnError();
	
	mysqli_bind_param($stmt, 'i', $itemID);
	mysqli_stmt_execute($stmt);	$this->throwExceptionOnError();
	mysqli_stmt_free_result($stmt);	mysqli_close($this->connection);  }

  public function updateEntity($item) {	$stmt = mysqli_prepare($this->connection,
		"UPDATE entity SET
			unique_id=?,name_vern=?,type=?,source_id=?");	$this->throwExceptionOnError();

	mysqli_bind_param($stmt, 'isii', $item->unique_id, $item->name_vern,
		$item->type, $item->source_id
	);	$this->throwExceptionOnError();
	mysqli_stmt_execute($stmt);	$this->throwExceptionOnError();
	mysqli_stmt_free_result($stmt);	mysqli_close($this->connection);  }

public function getNarratives($entityID) {
     $stmt = mysqli_prepare($this->connection,
          "
          
		SELECT DISTINCT

		attribute.data_char

		FROM

		attribute

		WHERE

		attribute.type = 46

		AND

		(attribute.entity_id =?

		OR

		attribute.target_entity =? )
		
		ORDER BY
		
		attribute.unique_id
          
           
           ");
      $this->throwExceptionOnError();
          
      mysqli_stmt_bind_param($stmt, 'ii', $entityID, $entityID);
      $this->throwExceptionOnError();

      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();

      $rows = array();
      mysqli_stmt_bind_result($stmt, $row->data_char);

      while (mysqli_stmt_fetch($stmt)) {
          $rows[] = $row;
          $row = new stdClass();
          mysqli_stmt_bind_result($stmt, $row->data_char);
      }
      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

      return $rows;

  }
  
    public function deleteAssoc($assocID) {
     $stmt = mysqli_prepare($this->connection,
          "
          
  DELETE
  
  FROM
  
  attribute
  
  WHERE
  
  attribute.unique_id = ".$assocID);

           $this->throwExceptionOnError();

      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();


      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

  }
     
  
  public function imageAssoc($imname) {
     $stmt = mysqli_prepare($this->connection,
          "
          
  SELECT DISTINCT

attribute.unique_id,
enttar.name_vern as actor,
entity.name_vern as location

FROM

attribute

LEFT JOIN entity
ON entity.unique_id = attribute.entity_id

LEFT JOIN entity as enttar
ON enttar.unique_id = attribute.target_entity

WHERE

attribute.data_char = ?

           ");
      $this->throwExceptionOnError();
          
      mysqli_stmt_bind_param($stmt, 's', $imname);
      $this->throwExceptionOnError();

      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();

      $rows = array();
      mysqli_stmt_bind_result($stmt, $row->unique_id, $row->actor, $row->location);

      while (mysqli_stmt_fetch($stmt)) {
          $rows[] = $row;
          $row = new stdClass();
          mysqli_stmt_bind_result($stmt, $row->unique_id, $row->actor, $row->location);
      }
      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

      return $rows;

  }
  
  

public function searchNarratives($name_vern) {

$substringArray = explode (" ", $name_vern);

/*$q = 0;
$max = count($substringArray);
$process = "'";
while ($q < $max) {
	
	//$slashString = str_replace("'", "", $substringArray[$q]);
	$slashString = addslashes($substringArray[$q]);
	
	$process = $process . "+" . $slashString;
	$process = $process . ",";
	$substringArray[$q] = $process;	
	$q++;
}
$procLength = strlen($process) - 1;
$process = substr($process, 0, $procLength);
$process = $process ."'";
*/
$process = "\"%".$name_vern."%\"";

     $stmt = mysqli_prepare($this->connection,
          "
select 
entity.name_vern,
attribute.data_char

from

attribute

LEFT JOIN entity
ON entity.unique_id = attribute.entity_id

WHERE

attribute.data_char LIKE (".$process.")
           
           ");
      $this->throwExceptionOnError();
          
//      mysqli_stmt_bind_param($stmt, 's', $name_vern);
//      $this->throwExceptionOnError();
//MATCH (attribute.data_char) AGAINST (".$process." in boolean mode)
      
      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();

      $rows = array();
      mysqli_stmt_bind_result($stmt, $row->actor, $row->narrative);

      while (mysqli_stmt_fetch($stmt)) {
          $rows[] = $row;
          $row = new stdClass();
          mysqli_stmt_bind_result($stmt, $row->actor, $row->narrative);
      }
      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

      return $rows;

  }

  public function getImDir(){
  
          $dir = "images/";
    
    $rows = array();
    $bupkis = 'fnamer';
    
    if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
    $q = 0;
     while (($file = readdir($dh)) !== false) {
            $rest = strtolower(substr($file, -3));
            $imageTypes = array("jpg", "png");
            if (strlen($rest) > 3) {
            
            }
            if (in_array($rest, $imageTypes))  {
            	$book = new stdClass;
            	$book->fnamer=$file;
              	$rows[] = $book;            	
            	$q++;
            	
            }
     }
        }
        closedir($dh);
    }

	return $rows;
	
  }
  
public function getImages($imageEntity) {
    $stmt = mysqli_prepare($this->connection,
          "

SELECT DISTINCT

attribute.data_char,
attribute.target_entity,
tar_ent.type AS tar_ent_type,
attribute.entity_id,
ent_id.type AS ent_id_type,
COALESCE(ent_id.name_vern, tar_ent.name_vern) as name_dat

FROM

attribute

LEFT JOIN entity AS tar_ent
ON tar_ent.unique_id = attribute.target_entity

LEFT JOIN entity AS ent_id
ON ent_id.unique_id = attribute.entity_id

WHERE

attribute.type = 45

AND

(attribute.entity_id =?

OR

attribute.target_entity =?)


ORDER BY
attribute.data_char
           
           ");
      $this->throwExceptionOnError();
          
      mysqli_stmt_bind_param($stmt, 'ii', $imageEntity, $imageEntity);
      $this->throwExceptionOnError();

      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();

      $rows = array();
      mysqli_stmt_bind_result($stmt, $row->data_char, $row->entity_id, $row->ent_id_type, $row->target_entity, $row->tar_ent_type, $row->name_dat);

      $dupcheck = "No";
      while (mysqli_stmt_fetch($stmt)) {
      
        $rows[] = $row;  
          $row = new stdClass();
          mysqli_stmt_bind_result($stmt, $row->data_char, $row->entity_id, $row->ent_id_type, $row->target_entity, $row->tar_ent_type, $row->name_dat);
      	$dupcheck = $row->data_char;
      }
      
      $q = 0;
      $r = 0;
      $rows_f = array();
      $dupe = "box";
      while ($q < count($rows)) {
      
      if ($dupe == $rows[$q]->data_char)
      {
      }
      else{
		$rows_f[$r] = $rows[$q];
      	$r++;
      }
      $dupe = $rows[$q]->data_char;
      
      $q++;      
      }
      
      
      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

      return $rows_f;

  }
  
  public function getSelectionImages($imageEntity) {
     $stmt = mysqli_prepare($this->connection,
          "
          
SELECT DISTINCT

attribute.data_char,
attribute.target_entity,
tar_ent.type AS tar_ent_type,
attribute.entity_id,
ent_id.type AS ent_id_type

FROM

attribute

LEFT JOIN entity AS tar_ent
ON tar_ent.unique_id = attribute.target_entity

LEFT JOIN entity AS ent_id
ON ent_id.unique_id = attribute.entity_id

WHERE

attribute.type = 45

AND

(
attribute.entity_id =?

OR

attribute.target_entity =?
)

           
           ");
      $this->throwExceptionOnError();
          
      mysqli_stmt_bind_param($stmt, 'ii', $imageEntity, $imageEntity);
      $this->throwExceptionOnError();

      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();

      $rows = array();
      mysqli_stmt_bind_result($stmt, $row->data_char, $row->entity_id, $row->ent_id_type, $row->target_entity, $row->tar_ent_type);

      while (mysqli_stmt_fetch($stmt)) {
          $rows[] = $row;
          $row = new stdClass();
          mysqli_stmt_bind_result($stmt, $row->data_char, $row->entity_id, $row->ent_id_type, $row->target_entity, $row->tar_ent_type);
      }
      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

      return $rows;
  }
  
  
  public function getLatLong($latlongEntity) {
     $stmt = mysqli_prepare($this->connection,
          "
          
SELECT DISTINCT

entity.name_vern,
entity.unique_id,
atllat.data_numeric AS llat,
atllong.data_numeric AS llong,
CONCAT(atimage.data_char) as entimage,
CONCAT(tarimage.data_char) as tarimage


FROM

entity

LEFT JOIN attribute as atllat
ON atllat.entity_id = entity.unique_id AND atllat.type = 41

LEFT JOIN attribute as atllong
ON atllong.entity_id = entity.unique_id AND atllong.type = 42

LEFT JOIN attribute as atimage
ON atimage.entity_id = entity.unique_id AND atimage.type = 45

LEFT JOIN attribute as tarimage
ON tarimage.target_entity = entity.unique_id AND tarimage.type = 45

WHERE

(atllat.data_numeric is not null

AND

atllong.data_numeric is not null)

AND

(atllat.entity_id =?

OR

atllat.target_entity =?

OR

atllat.entity_id = ANY (SELECT attribute.entity_id FROM attribute WHERE attribute.target_entity =?)

OR

atllat.target_entity = ANY (SELECT attribute.target_entity FROM attribute WHERE attribute.entity_id =?)

OR

atllat.target_entity = ANY (SELECT attribute.entity_id FROM attribute WHERE attribute.target_entity =?)

OR

atllat.entity_id = ANY (SELECT attribute.target_entity FROM attribute WHERE attribute.entity_id =?)
)
          
           
           ");
      $this->throwExceptionOnError();
          
      mysqli_stmt_bind_param($stmt, 'iiiiii', $latlongEntity, $latlongEntity, $latlongEntity, $latlongEntity, $latlongEntity, $latlongEntity);
      $this->throwExceptionOnError();

      mysqli_stmt_execute($stmt);
      $this->throwExceptionOnError();

      $rows = array();
      mysqli_stmt_bind_result($stmt, $row->name_vern, $row->unique_id, $row->llat, $row->llong, $row->entimage, $row->tarimage);

      while (mysqli_stmt_fetch($stmt)) {
          $rows[] = $row;
          $row = new stdClass();
          mysqli_stmt_bind_result($stmt, $row->name_vern, $row->unique_id, $row->llat, $row->llong, $row->entimage, $row->tarimage);
      }
      mysqli_stmt_free_result($stmt);
      mysqli_close($this->connection);

      return $rows;
  }
  
  
/** 
  * Utitity function to throw an exception if an error occurs 
  * while running a mysql command. 
  */ 
  private function throwExceptionOnError($link = null) { 
    if($link == null) { 
      $link = $this->connection; 
    } 
    if(mysqli_error($link)) { 
      $msg = mysqli_errno($link) . ": " . mysqli_error($link); 
      throw new Exception('MySQL Error - '. $msg); 
    }         
  } 
 
} 
?>
