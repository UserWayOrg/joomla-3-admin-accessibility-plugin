SHELL = /bin/sh

all: clean_all build clean_sources

clean_sources:
	rm -rf joomla-admin-accessibility-plugin

clean_all:
	rm -rf *.tar joomla-admin-accessibility-plugin

build:
	mkdir joomla-admin-accessibility-plugin;
	cp -r administrator joomla-admin-accessibility-plugin;
	cp -r userway.xml joomla-admin-accessibility-plugin;
	tar -zcvf joomla-admin-accessibility-plugin.tar.gz joomla-admin-accessibility-plugin;