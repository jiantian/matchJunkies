import sys
from itertools import groupby
import os

def query2team(query):
	team = query.title()
	if team == "Us" or team == "Usa" or team == "United States" \
		or team == "United States Of America" or team == "U.S.A." \
		or team == "Unitedstates":
		team = "USA"
	if team == "China":
		team = "China PR"
	if team == "Bosnia Herzegovina":
		team = "Bosnia-Herzegovina"
	if team == "Czech":
		team = "Czech Republic"
	if team == "Korea":
		team = "Korea Republic"
	if team == "Ireland":
		team = "Republic of Ireland"
	if team == "Ivorycoast":
		team = "Ivory Coast"
	if team == "Saudiarabia":
		team = "Saudi Arabia"
	if team == "Costarica":
		team = "Costa Rica"	
	return team

def getTeamDoc(team):
	f = open('invertedIndex.txt', 'r')
	lines = f.readlines()
	team_doc = {}
	for line in lines:
		strlist = line.strip().split('\t')
		if team.lower() == strlist[0].lower():
			for i in range(1,len(strlist), 2):
				team_doc[strlist[i]] = strlist[i+1]
	f.close()
	return team_doc

def unionDoc(team1, team2):
	team1_doc = getTeamDoc(team1)
	team2_doc = getTeamDoc(team2)
	keys_a = set(team1_doc.keys())
	keys_b = set(team2_doc.keys())
	intersection = keys_a & keys_b
	for item in intersection:
		if team1_doc[item] == '1':
			main = item
		else:
			side = item
	return main, side

def output(mainDoc, sideDoc, team1, team2):
	if 'Games won' in open(mainDoc).read():
		lines = open(mainDoc).readlines()
		parsing1 = False
		parsing2 = False
		heading = team1+" national team record vs. "+team2+":\n"
		content = [heading]
		for line in lines:
			if line.strip().startswith('Games won'):
				parsing1 = True
			if line.strip()=='':
				parsing1 = False
			if parsing1:
				content.append(line.strip())

			if line.strip().startswith('Top scorer'):
				parsing2 = True
			if parsing2:
				if line.strip().startswith('Top scorer'):
					str1 = team1+" "+line.strip()
					content.append(str1)
				else:
					content.append(line.strip())

		parsing3 = False
		lines = open(sideDoc).readlines()
		for line in lines:
			if line.strip().startswith('Top scorer'):
				parsing3 = True
			if line.strip()=='':
				parsing3 = False
			if parsing3:
				if line.strip().startswith('Top scorer'):
					str1 = team2+" "+line.strip()
					content.append(str1)
				else:
					content.append(line.strip())
		matches = highestScoringGame(mainDoc)
		if not matches == []:
			for match in matches:
				content.append(match)
	else:
		content = ["No match between "+team1+" and "+team2+"."]
	return content

def highestScoringGame(mainDoc):
	lines = open(mainDoc).readlines()
	parsing = False
	d = {}
	for line in lines:
		if line.strip()=='':
			parsing = False
		if parsing:
			if not line.strip()=='v':
				strlist = line.strip().split('\t')
				score = strlist[3]
				if not score.strip() == '':
					score1 = int(score.split(' ')[0].split('-')[0])
					score2 = int(score.split(' ')[0].split('-')[1])
					totalScore = score1 + score2
					d[line.strip()] = totalScore
		if line.strip().startswith('Date'):
			parsing = True
	matches_w_title = []
	if not d == {}:
		highest = max(d.values())
		matches = [k for k,v in d.items() if v == highest]
		matches_w_title = ['Highest scoring game:']
		matches_w_title.extend(matches)
	return matches_w_title

def printContent(content, team1):
	print "=========================================================================="
	for line in content:
		if team1 +' Top scorer' in line:
			print "\n"+line
		elif team2 +' Top scorer' in line:
			print "\n"+line
		elif line.startswith('Highest scoring game'):
			print "\n"+line
		else:
			print line
	print "=========================================================================="

def getMatchLinks(recordFile):
	lookup = "http://www.11v11.com/matches"
	links = []
	myFile = open(recordFile, 'r')
	for line in myFile:
		if lookup in line:
			links.append(line.strip())
	myFile.close()
	return links

def writeMatchLinks(recordFile):
	links = getMatchLinks(recordFile)
	f_out = open('links.txt','w')
	for link in links:
		f_out.write(link+"\n")
	f_out.close()

if __name__ == "__main__":
	if len(sys.argv) != 3:
		print "Usage: python search.py team1 team2"
		sys.exit()

	path = 'records/'
	query1 = sys.argv[1]
	query2 = sys.argv[2]
	team1 = query2team(query1)
	team2 = query2team(query2)

	main, side = unionDoc(team1, team2)
	mainDoc = path+main+".txt"
	writeMatchLinks(mainDoc)
	sideDoc = path+side+".txt"
	content = output(mainDoc, sideDoc, team1, team2)
	printContent(content, team1)
