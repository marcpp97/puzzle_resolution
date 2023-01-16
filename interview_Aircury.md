# Interview Aircury

### Explanation of the approach taken

Since I read about the problem, It has been clear to me how to solve it. This exercise had to be solved with recursive method. This is because the key of the exercise was to try all the different combinations of pieces there could be. This problem has a tree structure, this means that from each node (piece) other nodes are born. This continues till there are no pieces to insert left (wich would tell that that sequence is a solution) or the pieces left can't be placed (wich would mean that this solution is not correct).
During the process, each piece that has not been used is rotated and compared with the pieces around it to see if it can fit into the structure. The comparison of the pieces are made with the pieces above and to the left of this one, this is because the pieces are being inserted from the left to the right and from above to below so to the right and below the piece compared there are no pieces.
At the beggining of the process, before the recursive method starts, is inserted in the top left corner a piece. This is because the corners will not change once they are inserted correctly and this prevents the algorithm from returning the same solved puzzle as the solution, but rotated.

### Lessons learnt

I learnt a little bit more about PHP doing this problem. Moreover, I have also remembered how PHP works and some functionalities that I didn't know about.

### What Went Well

The way to solve it (recursively). The methods stablished. The initial structure I created.

### Even Better If

Improve the efficiency with which the algorithm finds all possible solutions.

### How could you improve the algorithm in the future

I use many variables and some of them are probably not necessary. Improve the way the pieces are rotated. Don't pass in each call to the recursive method so many lists.

### How long did you spend solving this problem?

Approximately 8 hours.

### Are there any changes that you think we should make to these instructions or anything that we should take into consideration in the future?

Specify if there could be puzzles with the width and the height different and provide some solutions of puzzle with the width and the height different.