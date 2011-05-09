#!/usr/bin/env python
#Adds symbolic links to assignments to section leaders' folders.  
#Used when we need to make a submissions2 folder, but submissions needs 
#to have every assignment in it for the sake of the paperless IG system.
#
#
#
# How to run:
# /afs/ir/class/cs198/cgi-bin/paperless/make-symlinks.py submissions submissions2 nameSurfer
# This preserves the capability to find the assignment by only appending a number.
#
import os, sys

#Returns a list of all section leaders inside rootFolder
def getSectionLeaders(rootFolder):
  return os.listdir(rootFolder)

#Gets the path of the assignment to link to
def getSymLinkPath(rootFolder, sectionLeader, assignmentName):
  return rootFolder + '/' + sectionLeader + '/' + assignmentName

def main():
  if len(sys.argv) != 4:
    print """Usage: make-symlinks.py submissions-root submissions-2-root assignment-name
  submissions-root: the folder that contains all section leaders that needs symlinks
  submissions-2-root: the folder that contains all section leaders that will be linked to
  assignment-name: the name of the assignment within a section leader's folder 
  append-name: this is appended to the name of the symbolic link (in case half-full folders exist in submissions-root/sl)
  each section leader folder must contain directories with the names of assignments"""
    sys.exit(1)
  submissionsRoot = os.path.abspath(sys.argv[1])
  submissions2Root = os.path.abspath(sys.argv[2])
  assignmentName = sys.argv[3]
  sectionLeaders = getSectionLeaders(submissionsRoot)
  for sectionLeader in sectionLeaders:
    if os.path.isdir(submissionsRoot + '/' + sectionLeader):
      os.chdir(submissionsRoot + '/' + sectionLeader)
      symbolicLinkPath = getSymLinkPath(submissions2Root, sectionLeader, assignmentName)
      os.symlink(symbolicLinkPath, assignmentName)

if __name__ == '__main__':
  main()

