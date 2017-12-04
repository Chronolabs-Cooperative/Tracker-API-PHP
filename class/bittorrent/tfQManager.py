#!/usr/bin/env python

# Written by kboy

# v 1.01 Mar 20, 06 

import commands, os, re, sys
from os import popen, getpid, remove
from sys import argv, stdout
from time import time, strftime
import thread, threading, time

DEBUG = False

if __debug__: LOGFILE=open(argv[1]+"tfQManager.log","w")

def run(qDirectory,maxSvrThreads,maxUsrThreads,sleepInterval,execPath):

    displayParams(qDirectory,maxSvrThreads,maxUsrThreads,sleepInterval,execPath)
    
    #Check to see if already running.
    lastPID = "0"
    try:
        f=open(qDirectory+"tfQManager.pid",'r')
        lastPID = f.readline().strip()
        f.close()
        if __debug__: traceMsg("Last QManager pid" + str(lastPID))
    except:
        pass
        
    if (int(lastPID) > 0):
        if (checkPIDStatus(lastPID) > 0):
            if __debug__: traceMsg("Already Running on pid:" + lastPID)
            raise KeyboardInterrupt

    if __debug__: traceMsg("QManager Starting")
    
    f=open(qDirectory+"tfQManager.pid",'w')
    f.write(str(getpid()) + "\n")
    f.flush()
    f.close()

    if __debug__: traceMsg("QManager PID :" + str(getpid()))

    # Extract from the execPath the Btphptornado.py script line.
    # this will be used during the process Counts to ensure we are 
    # unique from other running instances.

    ePath = execPath.split()
    btphp = ePath[-1]
    if __debug__: traceMsg("btphp ->"+btphp)

    if (1):
        try:    
            while 1:

                threadCount = checkThreadCount(btphp)
                if __debug__: traceMsg("CurrentThreadCount = " + str( threadCount ))

                #
                # Start Looping untill we have maxSvrThreads.
                # Or no Qinfo Files.
                #

                while int(threadCount) <= int(maxSvrThreads):
                    try:
                        # 
                        # Get the Next File.
                        # Check to see if we got a file back.
                        # if not break out of looping we don't have any files.
                        #
                        fileList = []
                        fileList = getFileList(qDirectory)
                        for currentFile in fileList:
                            if currentFile == "":
                                break
    
                            # set the name of the current statsFile
                            statsFile = currentFile.replace('/queue','').strip('.Qinfo')
                            if __debug__: traceMsg("statsFile = " + statsFile)
                
                            # 
                            # get the User name if we didn't get one 
                            # something was wrong with this file.
                            # 
                            currentUser = getUserName(statsFile)
                            if currentUser == "":
                                if __debug__: traceMsg("No User Found : " + currentFile)
                                # Prep StatsFile
                                updateStats(statsFile, '0')
                                removeFile(currentFile)
                                break
                            else:
                                if __debug__: traceMsg("Current User: " + currentUser)
    
                                #
                                # Now check user thread count
                                #
                                usrThreadCount = getUserThreadCount(currentUser, btphp)

                                #
                                # check UserThreadCount
                                #
                                if int(usrThreadCount) < int(maxUsrThreads):
                                    #
                                    # Now check to see if we start a new thread will we be over the max ?
                                    #
                                    threadCount = checkThreadCount(btphp)
                                    if int(threadCount) + 1 <= int(maxSvrThreads):
                                        if int(usrThreadCount) + 1 <= int(maxUsrThreads):

                                            cmdToRun = getCommandToRun(currentFile)
                                            #if __debug__: traceMsg(" Cmd :" + cmdToRun)

                                            if (re.search(currentUser,cmdToRun) == 0):
                                                if __debug__: traceMsg("Incorrect User found in Cmd")
                                                cmdToRun = ''
                                            if (re.search('\|',cmdToRun) > 0):
                                                if __debug__: traceMsg(" Failed pipe ")
                                                cmdToRun = ''
                                            else:
                                                cmdToRun = execPath + cmdToRun

                                            cmdToRun = cmdToRun.replace('TFQUSERNAME', currentUser)
                                            #if __debug__: traceMsg(" Cmd :" + cmdToRun)

                                            if cmdToRun != "":
                                                #PrepStatsFile
                                                updateStats(statsFile, '1')

                                                if __debug__: traceMsg("Fire off command")
                                                try:
                                                    garbage = doCommand(cmdToRun)

                                                    # 
                                                    # wait until the torrent process starts 
                                                    # and creates a pid file.
                                                    # once this happens we can remove the Qinfo.
                                                    # 
                                                    for i in range(5):
                                                        try:
                                                            time.sleep(2)
                                                            f=open(statsFile+".pid",'r')
                                                            f.close()
                                                            break
                                                        except:
                                                            if i == 5:
                                                                if __debug__: traceMsg("pid file not created.  Continue.")
                                                            continue

                                                    # Ok this one started Remove Qinfo File.
                                                    if __debug__: traceMsg("Removing : " + currentFile)
                                                    removeFile(currentFile)
                                                except:
                                                    continue
                                            else:
                                                # 
                                                # Something wrong with command file.
                                                # 
                                                if __debug__: traceMsg("Unable to obtain valid cmdToRun : " + currentFile)
                                                removeFile(currentFile)
                                        else:
                                            if __debug__: traceMsg("Skipping this file since the User has to many threads")
                                            if __debug__: traceMsg("Skipping : " + currentFile)

                                    else:
                                        if __debug__: traceMsg("Skipping this file since the Server has to many threads")
                                        if __debug__: traceMsg("Skipping : " + currentFile)
                        break
                
                    except:
                        break

                    threadCount = checkThreadCount(btphp)
                    if __debug__: traceMsg("CurrentThreadCount = " + str( threadCount ))
 
                if __debug__: traceMsg("Sleeping...")
                time.sleep(float(sleepInterval))
        except:
            removeFile(qDirectory+"tfQManager.pid")
    else:
        LOG = True
        if __debug__: traceMsg("Only supported client is btphptornado.")
        removeFile(qDirectory+"tfQManager.pid")
    
        
def checkThreadCount(btphp):

    if __debug__: traceMsg("->checkTreadCount")

    psLine = []
    line = ""
    counter = 0
    list = doCommand("ps x -o pid,ppid,command -ww | grep '" + btphp + "' | grep -v " + argv[0] + " | grep -v grep")

    try:
        for c in list:
            line += c
            if c == '\n':
                psLine.append(line.strip())
                line = ""

        # look for the grep line
        for line in psLine:
            if (re.search(btphp,line) > 0):
                # now see if this is the main process and not a child.
                if (re.search(' 1 /',line) > 0):
                    counter += 1
                    if __debug__: traceMsg(" -- Counted -- ")
            if __debug__: traceMsg(line)

    except:
        counter = 0
        
    return counter

def checkPIDStatus(pid):

    if __debug__: traceMsg("->checkPIDStatus (" + pid + ")")

    counter = 0
    list = doCommand("ps -p "+pid+" -o pid= -ww")

    try:
        counter = len(list)
    except:
        counter = 0
        
    return counter

    
def getUserThreadCount(userName, btphp):

    if __debug__: traceMsg("->getUserThreadCount (" + userName + ")")

    psLine = []
    line = ""
    counter = 0
    list = doCommand("ps x -o pid,ppid,command -ww | grep '" + btphp + "' | grep -v " + argv[0] + " | grep -v grep")

    try:
        for c in list:
            line += c
            if c == '\n':
                psLine.append(line.strip())
                line = ""

        # look for the grep line
        for line in psLine:
            if (re.search(btphp,line) > 0):
                # now see if this is the main process and not a child.
                if (re.search(' 1 /',line) > 0):
                    # look for the userName
                    if re.search(userName,line) > 0:
                        counter += 1
                        if __debug__: traceMsg(" -- Counted -- ")
            if __debug__: traceMsg(line)
    except:
        counter = 0

    if __debug__: traceMsg("->getUserThreadCount is (" + str(counter)+")")
        
    return counter

def removeFile(currentFile):

    if __debug__: traceMsg("->removeFile (" + currentFile + ")")
    os.remove(currentFile)

    return

def doCommand( command ): 

    if __debug__: traceMsg("->doCommand (" + command + ")")

    # 
    # Fire off a command returning the output
    #
    return popen(command).read()


def doCommandPID( command ): 

    if __debug__: traceMsg("->doCommandPID (" + command + ")")

    # 
    # Fire off a command returning the pid
    #
    #return popen2.Popen4(command).pid
    garbage = doCommand(command)
    
    return getPIDofCmd(command)
    

def getPIDofCmd(command):

    if __debug__: traceMsg("->getPIDofCmd (" + command + ")")
     
    cmdPID = ""
    psLine = []
    endOfPID = False
    line = ""
    
    list = doCommand("ps x -ww | grep \'"+command+"\' | grep -v grep")

    if DEBUG:
        print list

    try:
        for c in list:
            line += c
            if c == '\n':
                psLine.append(line.strip(' '))

        # look for the grep line
        for line in psLine:
            for e in line:
                if e.isdigit() and endOfPID == False:
                    cmdPID += e
                else:
                    endOfPID = True
                    break
    except:
        cmdPID = "0"

    if __debug__: traceMsg(cmdPID)

    return cmdPID

def getCommandToRun(currentFile):
    
    if __debug__: traceMsg("->getCommandToRun (" + currentFile + ")")

    #Open the File and return the Command to Run.
    cmdToExec = ""

    try:
        f=open(currentFile,'r')
        cmdToExec = f.readline()
        f.close
    
    except:
        cmdToExec = ""
    
    return cmdToExec


def getFileList(fDirectory):

    if __debug__: traceMsg("->getFileList (" + fDirectory + ")")

    # Get the list of Qinfo files.
    fileList = []

    try:
        times = {}
   
        for fName in os.listdir(fDirectory):
            if re.search('\.Qinfo',fName) > 0:
                p = os.path.join(fDirectory,fName)
                times.setdefault(str(os.path.getmtime(p)),[]).append(p)

        l = times.keys()
        l.sort()
 
        for i in l:
            for f in times[i]:
                fileList.append(f)
    except:
        fileList = ""
        
    return fileList


def getUserName(fName):

    if __debug__: traceMsg("->getUserName (" + fName + ")")

    userName = ""

    try:
        f=open(fName,'r')
        garbage = f.readline()
        garbage = f.readline()
        garbage = f.readline()
        garbage = f.readline()
        garbage = f.readline()
        userName = f.readline().strip()
        f.close()
        
        if __debug__: traceMsg("userName : " + userName)

    except:
        userName = ""

    return userName


def updateStats(fName, status = '1'):

    if __debug__: traceMsg("->updateStats (" + fName + ")")

    fp=open(fName,'r+')
    fp.seek(0)
    fp.write(status)
    fp.flush()
    fp.close


def displayParams(qDirectory,maxSvrThreads,maxUsrThreads,sleepInterval,execPath):

    if __debug__: 
        traceMsg("qDir   : " + qDirectory)
        traceMsg("maxSvr : " + str(maxSvrThreads))
        traceMsg("maxUsr : " + str(maxUsrThreads))
        traceMsg("sleepI : " + str(sleepInterval))
        traceMsg("execPath : " + str(execPath))
    return


def traceMsg(msg):
    if DEBUG:
       print msg
    if __debug__:
       LOGFILE.write(msg + "\n")
       LOGFILE.flush()
       
    
if __name__ == '__main__':
    run(argv[1],argv[2],argv[3],argv[4],argv[5])
