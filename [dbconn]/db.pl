#!/usr/bin/perl -w
use strict;
use Data::Dumper;
use DBI;
use LWP::UserAgent;

=begin
здесь тоже логин/пасс к базе менять. обязательно
в ботах юзается.
=cut

my %config = (
	db => 'tzpolice',
	host => '192.168.253.6',
	login => 'tzpolice',
	pass => 'uDKs8NthHdsMtBUW',

	db2 => 'tzpolice_test',
	host2 => '192.168.253.6',
	login2 => 'tzpolice_test',
	pass2 => 'oXNrFiUGWCoHEp9Z',
);

our $db = DBI->connect("DBI:mysql:database=$config{db};host=$config{host}", $config{login}, $config{pass}, {RaiseError => 1}) or die ('DB error!');
$db->do('SET NAMES cp1251');

our $db2 = DBI->connect("DBI:mysql:database=$config{db2};host=$config{host2}", $config{login2}, $config{pass2}, {RaiseError => 1}) or die ('DB2 error!');
$db2->do('SET NAMES cp1251');
$db2->do('SET wait_timeout=700');

1;