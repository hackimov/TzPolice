<?php

	class mod_rss	{
	
		##---------------------------------------------------------------------
		##	VARIABLES:
		##---------------------------------------------------------------------
		var $parser; // parser holder
		var $feed; // feed URL
		
		var $title; // item title
		var $description; // item content
		var $link; // item link
		var $pubDate; // item date of publishing
		var $insideitem = false; // are we inside ITEM?
		var $tag; // name of tag inside item
		var $items_max = 0; // max number of item we want to show
		var $items_num = 0; // current count of items
		var $direct = false; // direct output
		var $output = array(); // output array (if direct output is disabled)
		var $counter = 0; // counter for direct output array
	
	
		##---------------------------------------------------------------------
		##	FUNCTION: core_parser($feed, $max, $print)
		##---------------------------------------------------------------------
		function parser($feed, $max, $print)	{
			#-> handling wrong values
			#-> feed URL
			if (!is_string($feed)):
				#-> break script
				break;
			endif;
			#-> max items
			if (!is_int($max)):
				#-> set to 0
				$max = 0;
			endif;
			#-> direct output
			if (!is_bool($print)):
				#-> disable
				$print = false;
			endif;
			
			#-> create XML parser instance
			$this->parser = xml_parser_create();
			#-> assign feed source
			$this->feed = $feed;
			#-> allow using parser inside object
			xml_set_object($this->parser,&$this);
			#-> element handler functions
			xml_set_element_handler($this->parser, "core_beginElement", "core_endElement");
			#-> character data handler
			xml_set_character_data_handler($this->parser, "core_characterData");
			
			#-> set maximum of displayed items
			#-> we have to set it $max - 1, because counting starts with 0
			if ($max == 0 || !$max):
				$this->items_max = 0;
			else:
				$this->items_max = $max - 1;
			endif;
			
			#-> set direct output
			#-> true - direct output enabled; false - direct output disabled
			$this->direct = $print;
			
			#-> call source
			$this->core_loadFeed();
			
			return $this->output;
		}
		
		
		##---------------------------------------------------------------------
		##	FUNCTION: core_beginElement($parser, $tag)
		##---------------------------------------------------------------------
		function core_beginElement($parser, $tag) {
			#-> called when start tag is encountered
			if ($this->insideitem):
				$this->tag = $tag;
			#-> ITEM encountered
			elseif ($tag == "ITEM"):
				$this->insideitem = true;
			endif;
		}
	
	
		##---------------------------------------------------------------------
		##	FUNCTION: core_endElement($parser, $tag)
		##---------------------------------------------------------------------
		function core_endElement($parser, $tag)	{
			#-> called when end tag is encountered
			if ($tag == "ITEM" && $this->items_num <= $this->items_max):
				#-> direct output is enabled
				if ($this->direct == true):
					#-> title
					printf('<p class="header"><b><a href="%s">%s</a></b></p>'."\n",
						trim($this->link),
						htmlspecialchars(trim($this->title)));
					#-> description
					printf('<p class="content">%s</p>'."\n",
						htmlspecialchars(trim($this->description)));
					#-> date
					printf('<p class="date">%s</p>'."\n",
						htmlspecialchars(trim($this->pubDate)));
				#-> direct output is disabled
				else:
					#-> title
					$this->output[$this->counter]['title'] = $this->title;
					#-> description
					$this->output[$this->counter]['description'] = $this->description;
					#-> link
					$this->output[$this->counter]['link'] = $this->link;
					#-> date
					$this->output[$this->counter]['date'] = $this->pubDate;
					#-> counter for array
					$this->counter++;
				endif;
				#-> empty variables
				$this->title = NULL;
				$this->description = NULL;
				$this->link = NULL;
				$this->pubDate = NULL;
				$this->insideitem = false;
				#-> maximum of displayed items
				if ($this->items_max > 0):
					$this->items_num++;
				endif;
			endif;
		}	
		

		##---------------------------------------------------------------------
		##	FUNCTION: core_characterData($parser, $tag)
		##---------------------------------------------------------------------
		function core_characterData($parser, $data) {
			#-> if parser is inside item
			if ($this->insideitem):
				switch ($this->tag):
					#-> get title
					case "TITLE":
						$this->title .= $data;
						break;
					#-> get description
					case "DESCRIPTION":
						$this->description .= $data;
						break;
					#-> get link
					case "LINK":
						$this->link .= $data;
						break;
					#-> get date when item was publicated
					case "PUBDATE":
						$this->pubDate .= $data;
						break;
				endswitch;
			endif;
		}


		##---------------------------------------------------------------------
		##	FUNCTION: core_loadFeed()
		##---------------------------------------------------------------------
		function core_loadFeed(){
			#-> open file
			$fp = @fopen($this->feed,"r");
			#-> error handling
			if (!$fp):
				#-> break script
				break;
				#-> you can also use die()
				#die("Unexpected error while reading the Feed");
			endif;
			
			#-> read the content
			while ($data = fread($fp, 4096)):
				#-> put data to parser
				xml_parse($this->parser, $data, feof($fp));
			endwhile;
			#-> close the file
			fclose($fp);
			#-> free $fp
			#-> This is not necessary, but PHP will destroy only variables,
			#-> not their values. This is the way how we can handle that.
			$fp = NULL;
			unset($fp);
			#-> release parser
			xml_parser_free($this->parser);
		}
}

?>