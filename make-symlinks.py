#!/usr/bin/env python
#Adds symbolic links to assignments to section leaders' folders.  
#Used when we need to make a submissions2 folder, but submissions needs 
#to have every assignment in it for the sake of the paperless IG system.
#
#
#
# How to run:
# /afs/ir/class/cs198/cgi-bin/paperless/make-symlinks.py submissions submissions2 nameSurfer 2
# This preserves the capability to find the assignment by only appending a number.
#
import os, sys

#If path is an absolute path, returns path
#If path is a relative path, changes that path into an absolute path
def makeAbsolutePath(path):
  if path[0] == '/':
    return path
  return os.getcwd() + '/' + path

#Returns a list of all section leaders inside rootFolder
def getSectionLeaders(rootFolder):
  return os.listdir(rootFolder)

#Gets the path of the assignment to link to
def getSymLinkPath(rootFolder, sectionLeader, assignmentName):
  return rootFolder + '/' + sectionLeader + '/' + assignmentName

def main():
  if len(sys.argv) != 5:
    print """Usage: make-symlinks.py submissions-root submissions-2-root assignment-name append-name
  submissions-root: the folder that contains all section leaders that needs symlinks
  submissions-2-root: the folder that contains all section leaders that will be linked to
  assignment-name: the name of the assignment within a section leader's folder 
  append-name: this is appended to the name of the symbolic link (in case half-full folders exist in submissions-root/sl)
  each section leader folder must contain directories with the names of assignments"""
    sys.exit(1)
  submissionsRoot = makeAbsolutePath(sys.argv[1])
  submissions2Root = makeAbsolutePath(sys.argv[2])
  assignmentName = sys.argv[3]
  appendName = sys.argv[4]
  sectionLeaders = getSectionLeaders(submissionsRoot)
  for sectionLeader in sectionLeaders:
    os.chdir(submissionsRoot + '/' + sectionLeader)
    symbolicLinkPath = getSymLinkPath(submissions2Root, sectionLeader, assignmentName)
    os.symlink(symbolicLinkPath, assignmentName + appendName)

if __name__ == '__main__':
  main()

