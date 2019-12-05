#!/usr/bin/perl -w
use strict;
sub touch
{
 my $fname = shift;
 if (open my $file, ">>$fname") {
  close $file;
 }
}
touch("/home/sites/police/bot/tzbot.lock");
sleep 10;
unlink "/home/sites/police/bot/tzbot.lock";